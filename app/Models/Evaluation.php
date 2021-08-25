<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'questions',
        'html_form',
    ];

    protected $casts = [
        'questions' => 'array',
    ];

    protected $appends = [
        'questions_array'
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function getQuestionsArrayAttribute()
    {
        return json_decode($this->questions);
    }

}
