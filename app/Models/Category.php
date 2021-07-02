<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class category extends Model
{
    use HasFactory, SoftDeletes;

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
