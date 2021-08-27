<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventEvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Event $event)
    {
        return view('organizer.events.evaluations.index', compact('event'));
    }

    public function Update(Request $request, Event $event, Evaluation $evaluation)
    {
        DB::beginTransaction();

        if(! count($evaluation->questions_array)) {
            return redirect()->back()->with('message', 'The selected evaluation sheet has no entries. reuse of this sheet is not allowed');
        }

        $event->update([
            'evaluation_id' => $evaluation->id,
            'questions' => $evaluation->questions
        ]);

        DB::commit();

        return redirect()->route('organizer.events.evaluations.index', [$event->code])->with('message', $evaluation->name.' has been used as evaluation sheet.');

    }
}