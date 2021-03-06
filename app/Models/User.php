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
        'password',
        'attendee_organization_name', //! exclusively for attendees
        'attendee_occupation' //! exclusively for attendees
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
        'fullname',
        'profile_picture_path'
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
        return Str::title($this->firstname.' '.$this->lastname);
    }

    public function getProfilePicturePathAttribute()
    {
        return $this->profile_picture ? asset('storage/'.$this->profile_picture) : asset('assets/default-profile_picture.png');
    }

    //! ORGANIZER RELATIONSHIPS
    public function organization() //for organizer
    {
        return $this->hasOne(Organization::class, 'organizer_id');
    }

    public function evaluations() //for organizer
    {
        return $this->hasMany(Evaluation::class, 'organizer_id');
    }

    public function organizedEvents() //for organizer
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    //! ATTENDEE RELATIONSHIPS
    public function attendedEvents() //for attendees
    {
        return $this->belongsToMany(Event::class, 'event_attendees', 'attendee_id');
    }

    public function invitations() //for attendees
    {
        return $this->hasMany(Invitation::class, 'email', 'email');
    }

    public function eventEvaluations() //for attendees
    {
        return $this->hasMany(Evaluation::class, 'attendee_id');
    }
}
