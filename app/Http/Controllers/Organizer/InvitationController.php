<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Invitation;

use App\Jobs\SendEventInvitation;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    /**
     * Manage attendees of the resource
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function index(Event $event)
    {
        $event->load(['attendees']);

        return view('organizer.events.attendees', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $newly_invited = collect(json_decode($request->invitees))->pluck('email')->unique();

        $invitations = array_diff($newly_invited->toArray(), $event->invitations->pluck('email')->toArray()); //get only emai that has not been invited yet

        $recipients = collect($invitations)
            ->map(function($email) use ($event){
                return [
                    'event_id' => $event->id,
                    'email' => $email
                ];
            })
            ->toArray();

        Invitation::insert($recipients);

        $this->send($event, $recipients);

        return  redirect()->back()->with('message', 'Invitations are on their way!');
    }

    private function send(Event $event, $recipients)
    {
        SendEventInvitation::dispatch($event, Auth::user()->email, collect($recipients)->pluck('email'));
    }
}
