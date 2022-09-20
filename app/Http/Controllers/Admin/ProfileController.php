<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.profile.index');
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
            $path = $request->file('profile_picture')->store(
                "users/admin/$user->id/", 's3'
            );

            $user->profile_picture = [
                'filename' => basename($path),
                'path' => Storage::disk('s3')->url($path)
            ];
        }

        $user->save();

        return redirect()->route('admin.profile.index')->with('message', 'Profile Successfully Updated');
    }
}