<?php

namespace App\Http\Controllers\Attendee;

use App\Http\Controllers\Controller;
use App\Services\EventServices;
use App\Models\Event;
use App\Models\EventEvaluation;
use App\Models\EventSchedule;
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
    public function index(Request $request)
    {
        $attended_events = (new EventServices())
        ->getFrontEndEvents([
            'keyword'           => $request->filled('keyword') ? $request->keyword : false,
            'exclude_concluded' => false,
            'has_attended'      => true
        ])
        ->paginate(15);

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
            4. the user must have a record of attendance, and is an actual attendee user role
        */

        //! 1
        if(Carbon::parse($event->schedule_end)->isFuture()) {
            return redirect()->back()->with('message', 'Submitting Evaluation is not yet allowed.');
        }

        //! 2
        if(! eventHelperHasEvaluation($event)) {
            return redirect()->back()->with('message', 'This event does not have an evaluation sheet attached.');
        }

        //! 3
        if($event->evaluations->where('attendee_id', Auth::user()->id)->count()) {
            return redirect()->back()->with('message', 'You have already evaluated this event.');
        }

        //! 4
        if(in_array(Auth::user()->id, $event->attendees->pluck('attendee_id')->all())) {
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
