<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'mobile_number',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should appended
     *
     * @var array
     */
    protected $appends = [
        'fullname'
    ];

    public function setFirstnameAttribute($value)
    {
        $this->attributes['firstname'] = Str::lower($value);
    }

    public function setLastnameAttribute($value)
    {
        $this->attributes['lastname'] = Str::lower($value);
    }

    public function getFullnameAttribute()
    {
        return Str::title("$this->firtname $this->lastname");
    }

    public function getRoleAttribute()
    {
        return Str::title("$this->firtname $this->lastname");
    }
}
