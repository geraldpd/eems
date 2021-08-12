<?php

namespace App\Http\Controllers\Attendee;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attended_events = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('invitations', 'users.email', '=', 'invitations.email')
            ->join('events', 'invitations.event_id', '=', 'events.id')
            ->leftJoin('event_attendees', function($join) {
                $join->on('users.id', '=', 'event_attendees.attendee_id');
                $join->on('events.id', '=', 'event_attendees.event_id');
            })
            ->select(
                'users.id as user_id',
                'invitations.id as invitation_id',
                'event_attendees.is_confirmed as is_confirmed',
                'events.*',
            )
            ->get()
            ->map(function ($event) {
               $event->schedule_start = Carbon::parse($event->schedule_start);
               $event->schedule_end = Carbon::parse($event->schedule_end);
               return $event;
            });

        //dd($attended_events);
        return view('attendee.events.index', compact('attended_events'));
    }
}
