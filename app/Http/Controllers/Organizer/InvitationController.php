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

    public function print(Event $event, $filter = false)
    {
        $event->load(['invitations.guest', 'attendees', 'start', 'end']);

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
        $participants = $this->getParticipants($event, $filter);
        $filter = Str::title($filter);
        $path = "events/$event->id/";
        $file_name = $filter.' '.$event->name.' Attandence';

        if(! count($participants)) {
            return redirect()->back()->with('message', 'Nothing to download');
        }

        $csvFile = tmpfile();
        $csvPath = stream_get_meta_data($csvFile)['uri'];
        $handle = fopen($csvPath, 'w');

        fputcsv($handle, [$file_name]);

        fputcsv($handle, ['Response', 'Email', 'Name', 'Organization']);

        foreach($participants as $participant) {
            fputcsv($handle, [$participant['response'], $participant['email'],$participant['name'], $participant['organization']]);
        }

        fclose($handle);

        Storage::disk('s3')->putFileAs('', $csvPath, $path.'attendance.csv');
        return Storage::disk('s3')->download($path.'attendance.csv');
    }

    private function getParticipants(Event $event, $filter)
    {
        $attendees = $event->attendees;
        $is_past = $event->start->schedule_start->isPast();

        switch ($filter) {
            case 'pending':
                $participants = $event->invitations->map(function($invitation) use ($attendees, $is_past) {

                    //UNREGISTERED
                    if(! $invitation->guest) {
                        return [
                            'organization' => 'N/A',
                            'name' => 'N/A',
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            'response' => $is_past ? 'Declined' : 'Pending'
                        ];
                    }

                    //REGISTERED
                    if(! in_array($invitation->guest->email, $attendees->pluck('email')->all())) {
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
                $participants = $event->invitations->map(function($invitation) use ($attendees, $is_past) {

                    if(!$invitation->guest) return;

                    if( in_array($invitation->guest->email, $attendees->pluck('email')->all())) {
                        return [
                            'organization' => $invitation->guest->attendee_organization_name,
                            'name' => $invitation->guest->fullname,
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            'response' => 'Confirmed'
                        ];
                    }
                });
                break;

            case 'declined':
                //$participants = $event->invitations()->doesnthave('guest')->get();

                $participants = $event->invitations->map(function($invitation) use ($attendees, $is_past) {
                    if(! $invitation->guest && $is_past) { //the invitation has no relationship with the user(aka guest), it is understood that the guest did not confirmed the invitations
                        return [
                            'organization' => 'N/A',
                            'name' => 'N/A',
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            'response' => 'Declined'
                        ];
                    }

                    if($is_past && ! in_array($invitation->guest->email, $attendees->pluck('email')->all())) {
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
                //dd(1);
                $participants = $event->invitations->map(function($invitation) use ($attendees, $is_past) {

                    if(! $invitation->guest) {
                        return [
                            'organization' => 'N/A',
                            'name' => 'N/A',
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            'response' => $is_past ? 'Declined' : 'Pending'
                        ];
                    }

                    if( in_array($invitation->guest->email, $attendees->pluck('email')->all())) {
                        return [
                            'organization' => $invitation->guest->attendee_organization_name,
                            'name' => $invitation->guest->fullname,
                            'created_at' => $invitation->created_at,
                            'email' => $invitation->email,
                            'response' => 'Confirmed'
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
