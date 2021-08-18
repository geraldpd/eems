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
        'questions',
        'html_form',
    ];

    protected $casts = [
        'questions' => 'json',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class);
    }

}
