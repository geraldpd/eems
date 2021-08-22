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
    ];

    protected $casts = [
        'schedule_start' => 'datetime:Y-m-d H:i:s',
        'schedule_end' => 'datetime:Y-m-d H:i:s'
    ];

    protected $appends = [
        'group_date',
        'notif_confirmed_attendee_count',
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

    public function evaluations()
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
}
