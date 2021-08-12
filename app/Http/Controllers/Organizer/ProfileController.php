<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return view('organizer.profile.index');
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

        if($request->password && $request->password_confirmation) {
            $user->password =  Hash::make($request->password);
        }

        if($request->has('profile_picture')) {
            $location = "users/attendees/$user->id/";

            $path = $request->file('profile_picture')->store(
                $location, 'public'
            );

            $user->profile_picture = $path;
        }

        $user->save();

        return redirect()->route('organizer.profile.index')->with('message', 'Profile Successfully Updated');
    }

}