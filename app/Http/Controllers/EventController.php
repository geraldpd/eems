<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;

class EventController extends Controller
{

    public function index()
    {
        return view('front.events.index', compact('event'));
    }

    public function show(Event $event)
    {
        return view('front.events.show', compact('event'));
    }

    public function invitation(Event $event, $email)
    {
        // check if the email can be decrypted
        try {
            $email = decrypt($email);
        } catch (DecryptException $e) {
           return abort(404);
        }

        // check whether the email exists in the users table
        $invitee = User::whereEmail($email)->first();

        //when no user is found with that email
        if(! $invitee) {

            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('register', ['event' => $event->code, 'email' => $email]);
        }

        //
        Auth::login($invitee);

        //check if this email has already accepted the invitation for this event
        //$event->

    }
}
