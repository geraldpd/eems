<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_start',
        'schedule_end'
    ];

    protected $casts = [
        'schedule_start' => 'datetime:Y-m-d H:i:s',
        'schedule_end' => 'datetime:Y-m-d H:i:s'
    ];

    protected $appends = [
        'status'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function getStatusAttribute()
    {
        return eventScheduleStatus($this);
    }
}
