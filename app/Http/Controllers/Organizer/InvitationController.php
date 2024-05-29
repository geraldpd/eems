<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\Invitation;

use App\Jobs\SendEventInvitation;
use App\Mail\EventInvitationDecision;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    /**
     * Manage attendees of the resource
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function index(Event $event, $filter = false)
    {
        $event->load(['invitations.guest', 'attendees', 'start', 'end']);

        $participants = $this->getParticipants($event, $filter);

        resetNotifConfirmedAttendeeCount($event); //? reset the notification badge in /show to zero after viewing the /invitations page

        return view('organizer.events.invitation', compact('event', 'participants', 'filter'));
    }

    public function approveBooking(Event $event, Request $request)
    {
        $eventAttendee = EventAttendee::whereEventId($event->id)->whereAttendeeId($request->attendee_id)->first();

        if ($eventAttendee->is_booked) {
            return response()->json([
                'result' => 'fail',
                'message' => 'Booking already approved!'
            ]);
        }

        if ($event->booked_participants >= $event->max_participants) {
            return response()->json([
                'result' => 'fail',
                'message' => 'Maximum number of participants has been reached'
            ]);
        }

        $eventAttendee->is_booked = 1;
        $eventAttendee->is_confirmed = 1;
        $eventAttendee->is_disapproved = 0;
        $eventAttendee->save();

        $recipient = User::whereId($request->attendee_id)->first()->email;

        //dd($event, Auth::user()->email, $recipient, 'emails.invitations.approved');
        Mail::to($recipient)->send(new EventInvitationDecision($event, Auth::user()->email, $recipient, 'emails.invitations.approved', 'Booking Approved'));

        return response()->json([
            'result' => 'success',
            'message' => 'successfully_booked'
        ]);
    }

    public function disapproveBooking(Event $event, Request $request)
    {
        $eventAttendee = EventAttendee::whereEventId($event->id)->whereAttendeeId($request->attendee_id)->first();

        if ($eventAttendee->is_booked) {
            return response()->json([
                'result' => 'fail',
                'message' => 'Booking already approved!'
            ]);
        }

        if ($event->booked_participants >= $event->max_participants) {
            return response()->json([
                'result' => 'fail',
                'message' => 'Maximum number of participants has been reached'
            ]);
        }

        $eventAttendee->is_booked = 0;
        $eventAttendee->is_confirmed = 1;
        $eventAttendee->is_disapproved = 1;
        $eventAttendee->save();

        $recipient = User::whereId($request->attendee_id)->first()->email;

        Mail::to($recipient)->send(new EventInvitationDecision($event, Auth::user()->email, $recipient, 'emails.invitations.disapproved', 'Booking Disapproved', $request->reason));

        return response()->json([
            'result' => 'success',
            'message' => 'successfully_booked'
        ]);
    }

    public function print(Event $event, $filter = false)
    {
        $event->load(['organizer.organization', 'invitations.guest', 'attendees', 'start', 'end']);

        $participants = $this->getParticipants($event, $filter);

        return view('organizer.events.invitation-print', compact('event', 'participants', 'filter'));
    }

    public function store(Request $request, Event $event)
    {
        $newly_invited = collect(json_decode($request->invitees))->pluck('email')->unique();

        /*
            *get only the emails that has not been invited yet in case a duplicate email has been inserted
        */
        $invitations = array_diff($newly_invited->toArray(), $event->invitations->pluck('email')->toArray());

        $recipients = collect($invitations)
            ->map(function ($email) use ($event) {
                return [
                    'event_id' => $event->id,
                    'email' => $email,
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ];
            })
            ->toArray();

        Invitation::insert($recipients);

        SendEventInvitation::dispatch($event, Auth::user()->email, collect($recipients)->pluck('email'));

        return  redirect()->back()->with('message', 'Invitations are on their way!');
    }

    public function download(Event $event, $filter = 'all')
    {
        $participants = $this->getParticipants($event, $filter);
        $filter = Str::title($filter);
        $path = "events/$event->id/";
        $file_name = $filter . ' ' . $event->name . ' Attandence';

        if (!count($participants)) {
            return redirect()->back()->with('message', 'Nothing to download');
        }

        $csvFile = tmpfile();
        $csvPath = stream_get_meta_data($csvFile)['uri'];
        $handle = fopen($csvPath, 'w');

        fputcsv($handle, [$file_name]);

        fputcsv($handle, ['Response', 'Email', 'Name', 'Organization']);

        foreach ($participants as $participant) {
            fputcsv($handle, [$participant['response'], $participant['email'], $participant['name'], $participant['organization']]);
        }

        fclose($handle);

        Storage::disk('s3')->putFileAs('', $csvPath, $path . 'attendance.csv');
        return Storage::disk('s3')->download($path . 'attendance.csv');
    }

    private function getParticipants(Event $event, $filter)
    {
        $attendees = $event->attendees;
        $is_past = $event->start->schedule_start->isPast();

        switch ($filter) {
            case 'approved': //approved
                $participants = EventAttendee::query()
                    ->whereEventId($event->id)
                    ->whereIsConfirmed(1)
                    ->whereIsBooked(1)
                    ->with('attendee')
                    ->get()
                    ->map(function ($participant) {
                        return [
                            'organization' => $participant->attendee->attendee_organization_name,
                            'name' => $participant->attendee->fullname,
                            'created_at' => $participant->created_at,
                            'email' => $participant->attendee->email,
                            'response' => 'Approved'
                        ];
                    });
                break;
            case 'disapproved': //approved
                $participants = EventAttendee::query()
                    ->whereEventId($event->id)
                    ->whereIsConfirmed(1)
                    ->whereIsBooked(0)
                    ->whereIsDisapproved(1)
                    ->with('attendee')
                    ->get()
                    ->map(function ($participant) {
                        return [
                            'organization' => $participant->attendee->attendee_organization_name,
                            'name' => $participant->attendee->fullname,
                            'created_at' => $participant->created_at,
                            'email' => $participant->attendee->email,
                            'response' => 'Disapproved'
                        ];
                    });
                break;
            case 'pending':
                $participants = $event->invitations->map(function ($invitation) use ($attendees, $is_past) {

                    //UNREGISTERED
                    if (!$invitation->guest) {
                        return [
                            'organization' => 'N/A',
                            'name' => 'N/A',
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            'response' => $is_past ? 'Declined' : 'Pending'
                        ];
                    }

                    //REGISTERED
                    if (!in_array($invitation->guest->email, $attendees->pluck('email')->all())) {
                        return [
                            'organization' => $invitation->guest->attendee_organization_name,
                            'name' => $invitation->guest->fullname,
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            'response' => 'Pending'
                        ];
                    }
                });
                break;

            case 'confirmed':
                $participants = $event->invitations->map(function ($invitation) use ($attendees, $is_past) {

                    if (!$invitation->guest) return;

                    $confirmedAttendees =  $attendees->filter(function ($invitation) {
                        return $invitation->getOriginal('pivot_is_booked') === 0 && !$invitation->getOriginal('pivot_is_disapproved');
                    })->pluck('email')->all();

                    if (in_array($invitation->guest->email, $confirmedAttendees)) {
                        return [
                            'attendee_id'  => $invitation->guest->id,
                            'organization' => $invitation->guest->attendee_organization_name,
                            'name'         => $invitation->guest->fullname,
                            'created_at'   => $invitation->created_at,
                            'email'        => $invitation->email,
                            'response'     => 'Confirmed'
                        ];
                    }
                });
                break;

            case 'declined':
                //$participants = $event->invitations()->doesnthave('guest')->get();

                $participants = $event->invitations->map(function ($invitation) use ($attendees, $is_past) {
                    if (!$invitation->guest && $is_past) { //the invitation has no relationship with the user(aka guest), it is understood that the guest did not confirmed the invitations
                        return [
                            'organization' => 'N/A',
                            'name' => 'N/A',
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            'response' => 'Declined'
                        ];
                    }

                    if ($is_past && !in_array($invitation->guest->email, $attendees->pluck('email')->all())) {
                        return [
                            'organization' => $invitation->guest->attendee_organization_name,
                            'name' => $invitation->guest->fullname,
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            'response' => 'Declined'
                        ];
                    }
                });

                break;

            default: //all invited people
                $participants = $event->invitations->map(function ($invitation) use ($attendees, $is_past, $event) {

                    //not invited
                    if (!$invitation->guest) {
                        return [
                            'organization' => 'N/A',
                            'name' => 'N/A',
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            'response' => $is_past ? 'Declined' : 'Pending'
                        ];
                    }

                    //invited
                    if (in_array($invitation->guest->email, $attendees->pluck('email')->all())) {

                        $invited_user_id = $invitation->guest->id;

                        $bookedAttendee = EventAttendee::query()
                            ->whereEventId($event->id)
                            ->whereAttendeeId($invited_user_id)
                            // ->whereIsConfirmed(1)
                            // ->whereIsBooked(1)
                            //->select(['attendee_id', 'is_confirmed', 'is_disapproved'])
                            ->first();

                        switch (true) {
                            case $bookedAttendee->is_confirmed && $bookedAttendee->is_booked:
                                $response = 'Approved';
                                break;

                            case $bookedAttendee->is_confirmed && $bookedAttendee->is_disapproved:
                                $response = 'Disapproved';
                                break;

                            default:
                                $response = 'Confirmed';
                                break;
                        }

                        return [
                            'attendee_id' => $invited_user_id,
                            'organization' => $invitation->guest->attendee_organization_name,
                            'name' => $invitation->guest->fullname,
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            //'response' => in_array($invited_user_id, $bookedAttendees->pluck('attendee_id')->toArray()) ? 'Approved' : 'Confirmed'
                            'response' => $response
                        ];
                    }

                    return [
                        'organization' => $invitation->guest->attendee_organization_name,
                        'name' => $invitation->guest->fullname,
                        'created_at' => $invitation->created_at,
                        'email' => $invitation->email,
                        'response' => $is_past ? 'Declined' : 'Pending'
                    ];
                });

                break;
        }

        return $participants->sortBy('created_at')->filter()->toArray();
    }
}
