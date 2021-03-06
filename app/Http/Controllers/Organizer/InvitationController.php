<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use App\Models\Invitation;

use App\Jobs\SendEventInvitation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        $event->load(['invitations.guest', 'attendees']);

        $participants = $this->getParticipants($event, $filter);

        resetNotifConfirmedAttendeeCount($event); //? reset the notification badge in /show to zero after viewing the /invitations page

        return view('organizer.events.invitation', compact('event', 'participants', 'filter'));
    }

    public function store(Request $request, Event $event)
    {
        $newly_invited = collect(json_decode($request->invitees))->pluck('email')->unique();

        /*
            *get only the emails that has not been invited yet in case a duplicate email has been inserted
        */
        $invitations = array_diff($newly_invited->toArray(), $event->invitations->pluck('email')->toArray());

        $recipients = collect($invitations)
            ->map(function($email) use ($event){
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
        $path = "storage/events/$event->id/evaluation.csv";

        $handle = fopen($path, 'w');

        //adds the title
        fputcsv($handle, [$filter.' '.$event->name.' Invitations']);

        //adds a spacer
        fputcsv($handle, []);

        //headers
        $headers = array_values(['name']);
        //array_unshift($headers, 'Attendee');

        fputcsv($handle, $headers);

        $participants = $this->getParticipants($event, $filter);

        fputcsv($handle, array_column($participants, 'email'));

        fclose($handle);

        return [
            'path' => public_path($path),
            'extension_name' => '.csv'
        ];
    }

    private function getParticipants(Event $event, $filter)
    {
        $attendees = $event->attendees;
        $is_past = $event->schedule_start->isPast();

        switch ($filter) {
            case 'confirmed':
                $participants = $event->attendees->map(function($invitation) {
                    $invitation->response = 'Yes';

                    return [
                        'created_at' => $invitation->created_at,
                        'email' => $invitation->email,
                        'response' => 'Confirmed'
                    ];
                });

                break;
            case 'declined':
                //$participants = $event->invitations()->doesnthave('guest')->get();

                $participants = $event->invitations->map(function($invitation) use ($attendees, $is_past) {

                    if(! $invitation->guest) { //the invitation has no relationship with the user(aka guest), it is understood that the guest did not confirmed the invitations
                        return [
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            'response' => 'Confirmed'
                        ];
                    }

                    if(! in_array($invitation->guest->email, $attendees->pluck('email')->all())) {
                        return [
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            'response' => 'Confirmed'
                        ];
                    }
                });

                break;

            default: //all invited people
                //dd(1);
                $participants = $event->invitations->map(function($invitation) use ($attendees, $is_past) {
                    if(! $invitation->guest) {
                        return [
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            'response' => $is_past ? 'Declined' : 'Pending'
                        ];
                    }

                    if( in_array($invitation->guest->email, $attendees->pluck('email')->all())) {
                        return [
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            'response' => 'Confirmed'
                        ];
                    }

                    return [
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
