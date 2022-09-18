<?php

namespace App\Http\Controllers\Attendee;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $attendee = User::find(Auth::user()->id);
        return view('attendee.profile.index', compact('attendee'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'firstname' => ['required'],
            'lastname' => ['required'],
            'mobile_number' => ['required', 'digits:11', 'regex:/(09)[0-9]{9}/', 'numeric'],
            'password' => ['nullable', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->mobile_number = $request->mobile_number;
        $user->attendee_organization_name = $request->attendee_organization_name;
        $user->attendee_occupation = $request->attendee_occupation;

        if($request->password && $request->password_confirmation) {
            $user->password =  Hash::make($request->password);
        }

        if($request->has('profile_picture')) {
            $path = $request->file('profile_picture')->store(
                "users/attendees/$user->id/", 's3'
            );

            $user->profile_picture = [
                'filename' => basename($path),
                'path' => Storage::disk('s3')->url($path)
            ];
        }

        $user->save();

        return redirect()->back()->with('message', 'Profile Successfully Updated');


    }
}