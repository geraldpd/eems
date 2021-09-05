<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'attendee_id',
        'feedback',
    ];

    protected $casts = [
        'feedback' => 'json'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function attendee()
    {
        return $this->belongsTo(User::class);
    }
}
