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
        $events = Event::orderBy('schedule_start')
            ->with(['attendees'])
            ->withCount('attendees')
            ->get();

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
           return abort(404); //TODO: in the future updates, put more information why we returned 404
        }

        switch (true) {
            case $event->schedule_start->isPast():
                $message = 'Event has already concluded';
                break;

            case !$event->invitations()->whereEmail($email)->exists():
                //TODO: allow public invitation
                $message = 'You are not invited to this event';
                break;

            default:
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

                    $message = 'Successfuly confirmed invitation';
                break;
        }

        return redirect()->route('events.show', [$event->code])->with('message', $message);
    }
}
