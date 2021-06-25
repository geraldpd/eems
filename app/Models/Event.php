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
        'organizer_id',
        'category_id',
        'name',
        'type',
        'description',
        'location',
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
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
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
}
