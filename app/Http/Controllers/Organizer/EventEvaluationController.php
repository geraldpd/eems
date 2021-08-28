<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Event;
use Carbon\Carbon;
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @param  \App\Models\Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event, Evaluation $evaluation)
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event, Evaluation $evaluation)
    {

        $event->update([
            'evaluation_id' => null,
            'evaluation_questions' => null
        ]);


        return redirect()->route('organizer.events.evaluations.index', [$event->code])->with('message', 'Evaluation Successfully removed');
    }
}