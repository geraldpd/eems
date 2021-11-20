<?php

namespace App\Http\Controllers\Attendee;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventEvaluation;
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
            ->join('categories', 'events.category_id', 'categories.id')
            ->leftJoin('event_attendees', fn($join) => $join->on('users.id', '=', 'event_attendees.attendee_id')->on('events.id', '=', 'event_attendees.event_id'))
            ->select(
                'users.id as user_id',
                'invitations.id as invitation_id',
                'event_attendees.is_confirmed as is_confirmed',
                'categories.name as category_name',
                'events.*',
            )
            ->get()
            ->map(function ($event) {
               $event->schedule_start = Carbon::parse($event->schedule_start);
               $event->schedule_end = Carbon::parse($event->schedule_end);
               $event->dynamic_status = eventHelperGetDynamicStatus($event);
               $event->has_evaluation = eventHelperHasEvaluation($event);
               $event->uploaded_documents = eventHelperGetUploadedDocuments($event);
               $event->evaluated_attendees = DB::table('event_evaluations')->where('event_id', $event->id)->pluck('attendee_id')->all() ?? [];

               return $event;
            });

        //dd($attended_events);
        return view('attendee.events.index', compact('attended_events'));
    }

    public function evaluation(Request $request, Event $event)
    {
        if(!$event->evaluation_questions || !$event->evaluation_id) {
            return redirect()->back()->with('message', 'This event does not have an evaluation sheet');
        }

        return view('attendee.events.evaluation', compact('event'));
    }

    public function evaluate(Request $request, Event $event)
    {
        /*
            Check if the event cant be evalauted using the following conditions
            1. event must be concluded(schedule_start && schedule_end ->ispast())
            2. the event must have an evaluation sheet($sheet->has_evaluation)
            3. the user has not evaluated this event
            3. the user must have a record of attendance, and is an actual attendee user role
        */

        //! 1
        if(! $event->schedule_end->isPast()) {
            return redirect()->back()->with('message', 'Submitting Evaliation is not yet allowed.');
        }

        //! 2
        if(! eventHelperHasEvaluation($event)) {
            return redirect()->back()->with('message', 'This event does not have an evaluation sheet attaced.');
        }

        //! 3
        $evaluated_attendees = DB::table('event_evaluations')->where('event_id', $event->id)->pluck('attendee_id')->all() ?? [];
        if(in_array(Auth::user()->id, $evaluated_attendees)) {
            return redirect()->back()->with('message', 'You have already evaluated this event.');
        }

        //! 4
        if(in_array(Auth::user()->id, $event->attendees->pluck('id')->all())) {
            return redirect()->back()->with('message', "You have no participation for $event->name, submitting evaluation is prohibited.");
        }

        DB::beginTransaction();

        EventEvaluation::create([
            'event_id' => $event->id,
            'attendee_id' => Auth::user()->id,
            'feedback' => $request->except('_token'),
        ]);

        DB::commit();

        return redirect()->route('attendee.events.index')->with('message', 'Thank you for giving us your feedback.');
    }
}
