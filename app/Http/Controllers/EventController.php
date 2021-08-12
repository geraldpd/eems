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

    public function show(Request $request, Event $event)
    {
        if($request->has('invite')) { //scanned from qrcode

            if(Auth::check() && Auth::user()->roles()->first()->name == 'attendee') {

                $this->attend($event, Auth::user()->email, true);

            } else {

                return redirect()->route('login');

            }
        }

        return view('front.events.show', compact('event'));
    }

    //invitaion from email
    public function invitation(Event $event, $encrypted_email)
    {
        try {
            $email = decrypt($encrypted_email);
        } catch (DecryptException $e) {
           return abort(404); //TODO: in the future updates, put more information why we returned 404
        }

        $this->attend($event, $email);

    }

    private function attend(Event $event, $email, $is_qrcode_scanned = false)
    {
        $invitee = User::whereEmail($email)->first();

        switch (true) {
            case $event->schedule_start->isPast(): //event has concluded
                $message = 'Event has already concluded';
                break;

            case !$event->invitations()->whereEmail($email)->exists(): //email is not invited in this event

                if($is_qrcode_scanned) {

                    $event->invitations()->create([
                        'email' => $email
                    ]);

                    $event->attendees()->attach($invitee->id, [
                        'is_confirmed'=> 1
                    ]);

                    $message = 'You have been invited to this event.';

                } else {//email is not invited, and not qrcode scanned

                    //TODO: allow public invitation
                    $message = 'You are not invited to this event.';
                }
                break;

            default:
                    if($event->attendees()->find($invitee->id)) {

                        $message = 'You will be attending this event';
                        break;
                    }

                    if(! $invitee) {

                        Auth::logout();
                        request()->session()->invalidate();
                        request()->session()->regenerateToken();

                        return redirect()->route('register', ['event' => $event->code, 'email' => encrypt($email)]);
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
