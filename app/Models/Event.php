<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    const Pending = 'Pending'; //initial status
    const Active = 'Active';
    const Done = 'Done';
    const Cancelled = 'Cancelled';

    protected $fillable = [
        'code',
        'qrcode',
        'organizer_id',
        'category_id',
        'name',
        'type',
        'description',
        'location',
        'venue', //depending on the location field
        'online', //depending on the location field
        'documents',
        'schedule_start',
        'schedule_end',
        'status',

        'evaluation_id',
        'evaluation_name', // the final name of the evalaution used at the time setup
        'evaluation_description', // the final description of the evalaution used at the time setup
        'evaluation_questions', // the final questions of the evalaution used at the time setup
    ];

    protected $casts = [
        'schedule_start' => 'datetime:Y-m-d H:i:s',
        'schedule_end' => 'datetime:Y-m-d H:i:s',
        'evaluation_questions' => 'json',
    ];

    protected $appends = [
        'group_date',
        'notif_confirmed_attendee_count',
        'has_evaluation',
        'evaluation_questions_array',
        'uploaded_documents',
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

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function evaluation() //the evaluation sheet used
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function evaluations()// the survey that the attendees provided
    {
        return $this->hasMany(EventEvaluation::class);
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_attendees', 'event_id', 'attendee_id')
                ->withPivot('is_confirmed', 'id')
                ->withTimestamps();
    }

    public function getGroupDateAttribute()
    {
        return $this->schedule_start->format('d-m-y');
    }

    public function getNotifConfirmedAttendeeCountAttribute()
    {
        return $this->attendees()->whereIsConfirmed(1)->whereIsNotified(0)->count();
    }

    public function getEvaluationQuestionsArrayAttribute()
    {
        return $this->evaluation_questions;
        return json_decode($this->evaluation_questions, true);
    }

    public function getHasEvaluationAttribute()
    {
        return eventHelperHasEvaluation($this);
    }

    function getUploadedDocumentsAttribute()
    {
        $event = $this;
        $event_document_path = "storage/events/$event->id/documents";
        $documents = array_diff(scandir($event_document_path), array('.', '..'));

        $document_paths = collect();
        collect(array_values($documents))->map(function($document) use ($document_paths, $event_document_path) {
            $document_paths->put($document, [
                'public' => public_path("$event_document_path/$document"),
                'asset' => asset("$event_document_path/$document")
            ]);
        });

        return $document_paths->all();
    }
}
