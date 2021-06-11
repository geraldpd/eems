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
        'schedule_start' => 'date',
        'schedule_end' => 'date'
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

    public function getGroupDateAttribute()
    {
        return $this->schedule_start->format('d-m-y');
    }
}
