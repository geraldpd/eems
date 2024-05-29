<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EventAttendee extends Pivot
{
    protected $table = 'event_attendees';

    use HasFactory;

    protected $fillable = [
        'event_id',
        'attendee_id',
        'is_confirmed',
        'is_booked',
        'is_disapproved',
        'is_notified',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function attendee()
    {
        return $this->belongsTo(User::class, 'attendee_id');
    }
}
