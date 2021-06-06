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

    public function organizer()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
