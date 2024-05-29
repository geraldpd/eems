<?php

namespace App\Models;

use App\Services\EventServices;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    const Pending = 'Pending'; //initial status
    const Active = 'Active';
    const Done = 'Done';
    const Cancelled = 'Cancelled';

    //global event status
    const PENDING = 'PENDING';
    const ONGOING = 'ONGOING';
    const CONCLUDED = 'CONCLUDED';
    const CANCELLED = 'CANCELLED';

    protected $fillable = [
        'code',
        'qrcode',
        'organizer_id',
        'category_id',
        'name',
        'type_id',
        'description',
        'location',
        'venue', //depending on the location field
        'online', //depending on the location field
        'documents',
        'status',
        'max_participants',

        'evaluation_id',
        'evaluation_name', // the final name of the evaluation used at the time setup
        'evaluation_description', // the final description of the evaluation used at the time setup
        'evaluation_questions', // the final questions of the evaluation used at the time setup
        'evaluation_html_form', // the final html form that the attendee will fill on evaluation
        'evaluation_is_released', // control over the evaluation sheet, whether it can be evaluated or not
    ];

    protected $casts = [
        'banner' => 'json',
        'evaluation_questions' => 'json',
        'schedule_start' => 'datetime:Y-m-d H:i:s',
        'schedule_end' => 'datetime:Y-m-d H:i:s'
    ];

    protected $appends = [
        'dynamic_status',
        'notif_confirmed_attendee_count',
        'has_evaluation',
        'evaluation_questions_array',
        'uploaded_documents',
        'schedule_start', //fetches the first event_schedules relationship, returns schedule_start column
        'schedule_end', //fetches the last event_schedules relationship, returns schedule_end column
        'todays_scheduled_event',
        'attendance_percentage',
        'feedback_percentage',
        'qr_code_path',
        'banner_path',
        'booked_participants'
        //'has_attendee_event_rating'
    ];

    public function getRouteKeyName()
    {
        return 'code';
    }

    public function organizer()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function schedules()
    {
        return $this->hasMany(EventSchedule::class);
    }

    public function start()
    {
        return $this->hasOne(EventSchedule::class)->orderBy('schedule_start')->limit(1);
    }

    public function end()
    {
        return $this->hasOne(EventSchedule::class)->orderByDesc('schedule_end')->limit(1);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function evaluation() //the evaluation sheet used
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function evaluations() // the survey that the attendees provided
    {
        return $this->hasMany(EventEvaluation::class);
    }

    public function ratings()
    {
        return $this->hasMany(EventRating::class);
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_attendees', 'event_id', 'attendee_id')
            ->withPivot('is_booked', 'is_confirmed', 'is_disapproved','id')
            ->withTimestamps();
    }

    public function getNotifConfirmedAttendeeCountAttribute()
    {
        return $this->attendees()->whereIsConfirmed(1)->whereIsNotified(0)->count();
    }

    public function getEvaluationQuestionsArrayAttribute()
    {
        $evaluation_questions = gettype($this->evaluation_questions) === 'string' ? json_decode($this->evaluation_questions, true) : $this->evaluation_questions;

        return collect($evaluation_questions)->mapWithKeys(function ($item, $key) {
            $key = array_keys($item)[0];
            $value = array_values($item)[0];
            return [$key => $value];
        });
    }

    public function getHasEvaluationAttribute()
    {
        return eventHelperHasEvaluation($this);
    }

    function getUploadedDocumentsAttribute()
    {
        return (new EventServices)->getEventDocs($this);
        //return eventHelperGetUploadedDocuments($this);
    }

    public function getScheduleStartAttribute()
    {
        return $this->schedules->first()->schedule_start->format('Y-m-d H:i');
    }

    public function getScheduleEndAttribute()
    {
        return $this->schedules->last()->schedule_end->format('Y-m-d H:i');
    }

    public function getTodaysScheduledEventAttribute()
    {
        //fetches scheduled event for the current day
        return $this->schedules()
            ->whereDate('schedule_start', '>=', Carbon::now()->startOfDay())
            ->whereDate('schedule_end', '<=', Carbon::now()->endOfDay())
            ->first();
    }

    public function getBookedParticipantsAttribute()
    {
        return $this->attendees()->whereIsBooked(1)->count();
    }

    public function scopePendingEvents($query)
    {
        return $query->whereHas('schedules', function ($query) {
            $query->whereDate('schedule_start', '>', Carbon::now());
        });
    }

    public function getDynamicStatusAttribute()
    {
        $start = Carbon::parse($this->schedule_start);
        $end = Carbon::parse($this->schedule_end);

        switch (true) {
            case $start->isFuture():
                return self::PENDING;
                break;

            case $start->isPast() && $end->isFuture():
                return self::ONGOING;
                break;

            case $start->isPast() && $end->isPast():
                return self::CONCLUDED;
                break;
        }
    }

    public function getAttendancePercentageAttribute()
    {
        if ($this->attendees->count() && $this->invitations->count()) {
            $booked = $this->attendees->filter(function ($invitation) {
                return $invitation->getOriginal('pivot_is_booked') === 1;
            });

            $percentage = $booked->count() / $this->max_participants * 100;

            return number_format($percentage, 2, '.', '');
        }
    }

    public function getFeedbackPercentageAttribute()
    {
        if ($this->dynamic_status == 'CONCLUDED' && $this->evaluations->count() != 0) {

            $percentage = $this->attendees->count() / $this->evaluations->count() * 100;

            return number_format($percentage, 2, '.', '');
        }

        return 0;
    }

    public function getQrCodePathAttribute()
    {
        $s3_file_path = "events/$this->id/qrcode.svg";
        return Storage::disk('s3')->temporaryUrl($s3_file_path, now()->addMinutes(5));
    }

    public function getBannerPathAttribute()
    {
        if (!$this->banner) {
            return '';
        }

        $filename = $this->banner['filename'];
        $s3_file_path = "events/$this->id/$filename";
        return Storage::disk('s3')->temporaryUrl($s3_file_path, now()->addMinutes(5));
    }
}
