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

            if(Auth::check()) {//check if someone is logged in

                if(Auth::user()->roles()->first()->name == 'attendee') { //if logged in user is attendee, run attend()
                    return $this->attend($event, Auth::user()->email, true);
                }

            } else { //redirect to login

                return redirect()->route('login');

            }
        }

        $event->load(['invitations', 'attendees']);

        //show the event
        return view('front.events.show', compact('event'));
    }

    //invitaion from email
    public function invitation(Event $event, $encrypted_email)
    {
        try {
            $email = decrypt($encrypted_email);
        } catch (DecryptException $e) {
            return abort(404); //TODO: in the future updates, put more information why we returned 404a
        }

        if(Auth::check()) {
            if(Auth::user()->email != $email) { //user tried clicked an invitaion that is not for him/her
                return redirect()->route('events.show', [$event->code]);
            }
        }

        return $this->attend($event, $email);
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

                    $event->invitations()->create(['email' => $email]);
                    $event->attendees()->attach($invitee->id, [
                        'is_confirmed'=> 1
                    ]);
                    $event->save();

                    $message = 'You have been invited to this event.';

                } else {//email is not invited, and not qrcode scanned

                    //TODO: allow public invitation
                    $message = 'You are not invited to this event.';
                }
                break;

            default:
                if(! $invitee) { //invited user is not yet registerd

                    Auth::logout();
                    request()->session()->invalidate();
                    request()->session()->regenerateToken();

                    return redirect()->route('register', ['event' => $event->code, 'email' => encrypt($email)]);
                }

                if($event->attendees()->whereAttendeeId($invitee->id)->exists()) { //user has already accepted the invitation

                    $message = 'You will be attending this event';

                } else { //login the user and confirm the attendance

                    Auth::login($invitee);

                    $event->attendees()->attach($invitee->id, [
                        'is_confirmed'=> 1
                    ]);
                    $event->save();

                    $message = 'Successfuly confirmed invitation';
                }

                break;
        }

        return redirect()->route('events.show', [$event->code])->with('message', $message);
    }
}
