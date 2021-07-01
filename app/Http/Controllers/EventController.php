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
        $events = Event::query();

        return view('front.events.index', compact('events'));
    }

    public function show(Event $event)
    {
        return view('front.events.show', compact('event'));
    }

    public function invitation(Event $event, $encrypted_email)
    {
        try {
            $email = decrypt($encrypted_email);
        } catch (DecryptException $e) {
           return abort(404); //in the future updates, put more information why we returned 404
        }

        $has_invitation = $event->invitations()->whereEmail($email)->exists();
        if(! $has_invitation) {
            return redirect()->route('events.show', [$event->code])->with('message', 'OoOps!You are not invited to this event');
        }

        $invitee = User::whereEmail($email)->first();

        if(! $invitee) {

            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('register', ['event' => $event->code, 'email' => $encrypted_email]);
        }

        Auth::login($invitee);

        $event->attendees()->attach($invitee->id, [
              'is_confirmed'=> 1
        ]);

        return redirect()->route('events.show', [$event->code])->with('message', 'Successfuly confirmed invitation');
    }
}
