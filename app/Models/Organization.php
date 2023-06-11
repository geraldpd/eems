<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Organization extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'department',
        'logo',
        'supporting_documents'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'logo' => 'json',
        'supporting_documents' => 'json',
    ];


    /**
     * The attributes that should appended
     *
     * @var array
     */
    protected $appends = [
        'logo_path',
        'supporting_documents_path'
    ];

    public function getLogoPathAttribute()
    {
        if (!$this->logo) {
            return '';
        }

        if (!$this->organizer->hasRole('organizer')) {
            return '';
        }

        $organizer_id = $this->organizer->id;
        $default_path = "users/organizers/$organizer_id/logo/";

        $s3_file_path = $default_path . $this->logo['filename'];

        return Storage::disk('s3')->temporaryUrl($s3_file_path, now()->addMinutes(5));
    }

    public function getSupportingDocumentsPathAttribute()
    {
        //return '';

        if (!$this->supporting_documents) {
            return '';
        }

        if ($this->organizer->hasRole('attendee')) {
            return '';
        }

        $organizer_id = $this->organizer->id;

        return collect($this->supporting_documents)->mapWithKeys(function ($docs) use ($organizer_id) {
            $default_path = "users/organizers/$organizer_id/supporting_documents/";
            $s3_file_path = $default_path . $docs['filename'];
            return [$docs['filename'] => Storage::disk('s3')->temporaryUrl($s3_file_path, now()->addMinutes(5))];
        });
    }

    public function organizer()
    {
        return $this->belongsTo(User::class);
    }
}
