<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendEventInvitation;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\EventAttendee;
use App\Models\EventSchedule;
use App\Models\Event;
use App\Models\User;
use App\Services\EventServices;
use Carbon\Carbon;
use Throwable;

class EventController extends Controller
{
    public function index(Request $request)
    {
        // $events = (new EventServices())
        // ->getFrontEndEvents([
        //     'keyword'           => $request->filled('keyword') ? $request->keyword : false,
        //     'exclude_concluded' => true,
        //     'has_attended'      => false
        // ])
        // ->paginate(15);

        $events = (new EventServices())->getFrontEndEventsPerDay([
            'keyword'           => $request->filled('keyword') ? $request->keyword : false,
            'exclude_concluded' => true,
            'has_attended'      => false
        ])->paginate(15);

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

        $event->evaluated_attendees = $event->evaluations->pluck('attendee_id')->all() ?? []; //ids of attendees that has evalauted this event

        $event->load(['invitations', 'attendees', 'schedules']);

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

    public function book(Event $event, Request $request)
    {
        if($event->invitations()->whereIn('email', $request->email)->exists()) {
            return redirect()->back()->with('message', 'Error! You tried to book an attendee that has already been invited.');
        }

        DB::beginTransaction();

        try {

            //create the data for the invitees
            $invitees = collect($request->email)
            ->map(function($email) use ($event){
                return [
                    'event_id' => $event->id,
                    'email' => $email,
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ];
            })
            ->toArray();

            //insert to database
            $event->invitations()->insert($invitees);

            //collect the invitation
            $invitations = collect($invitees)->pluck('email');

            //check if the user booked for himself
            if (in_array(Auth::user()->email, $request->email)) {
                //automatically accept the invitation

                //create event_attendees record
                EventAttendee::create([
                    'event_id' => $event->id,
                    'attendee_id' => Auth::user()->id,
                    'is_confirmed' => true,
                ]);

                //remove the current user from the invitation, so they dont get sent an email since they are auto conmfirmed
                $invitations = $invitations->filter(fn($email) => $email !== Auth::user()->email);
            }

            DB::commit();

            //if there is still invitations, send them
            if(count($invitations)) {

                SendEventInvitation::dispatch($event, Auth::user()->email, $invitations);

            }

            return redirect()->back()->with('message', 'You have successfully booked for this event');

        } catch (Throwable $th) {

            DB::rollBack();
            return redirect()->back()->with('message', $th->getMessage());
        }

    }

    public function acceptBookingInvitation(Event $event)
    {
        $event->attendees()->attach(Auth::user()->id, [
            'is_confirmed'=> 1,
        ]);

        $event->save();

        return redirect()->route('events.show', [$event->code])->with('message', 'Successfuly confirmed invitation');
    }

    private function attend(Event $event, $email, $is_qrcode_scanned = false)
    {
        $invitee = User::whereEmail($email)->first();

        switch (true) {
            case $event->end->schedule_end->isPast(): //event has concluded
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