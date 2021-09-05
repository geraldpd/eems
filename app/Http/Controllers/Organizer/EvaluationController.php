<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Evaluation\StoreRequest;
use App\Http\Requests\Evaluation\UpdateRequest;
use App\Models\Evaluation;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //dd($request->all());
        $evaluations = Auth::user()->evaluations;
        $event = $request->has('event') ? $this->getEvent($request->event) : null;

        return view('organizer.evaluations.index', compact('evaluations', 'event'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $event = $request->has('event') ? $this->getEvent($request->event) : null;

        return view('organizer.evaluations.create', compact('event'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        DB::beginTransaction();

        $evaluation = Auth::user()->evaluations()->create($request->validated());
        $params = [$evaluation->id];

        if($request->has('event')) {
            $event = $this->getEvent($request->event);

            $event->update([
                'evaluation_id' => $evaluation->id
            ]);

            $params = [$evaluation->id, 'event' => $event->code];
        }

        DB::commit();
        return redirect()->route('organizer.evaluations.edit', $params)->with('message', 'Evaluation Successfully Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function show(Evaluation $evaluation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Evaluation $evaluation)
    {
        $event = null;

        if($request->has('event')) {
            $event = $this->getEvent($request->event);

            $event->update([
                'evaluation_id' => $evaluation->id
            ]);
        }

        $evaluation->loadCount('events');
        $evaluation->pending_events = $this->getPendingEvents($evaluation)->get();;

        return view('organizer.evaluations.edit', compact('evaluation', 'event'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Evaluation $evaluation)
    {
        DB::beginTransaction();

        $params = [$evaluation->id];

        $evaluation->update($request->validated());

        //means evaluation is being modified for an event
        if($request->has('event')) {
            $event = $this->getEvent($request->event);

            if($event->schedule_start->isPast()) {
                return redirect()->back()->with('message', 'This event does not have an evaluation sheet');
            }

            $event->update([
                'evaluation_name' => $evaluation->name,
                'evaluation_description' => $evaluation->description,
                'evaluation_questions' => $evaluation->questions,
                'evaluation_html_form' => $evaluation->html_form
            ]);

            $params = [$evaluation->id, 'event' => $event->code];
        }

        if($this->getPendingEvents($evaluation)->count()){

            $evaluation
            ->events()
            ->where('schedule_start', '>', Carbon::now()) //TODO add additional condition, i.e. events with staus = pending
            ->update([
                'evaluation_name' => $evaluation->name,
                'evaluation_description' => $evaluation->description,
                'evaluation_questions' => $evaluation->questions,
                'evaluation_html_form' => $evaluation->html_form
            ]);

        }

        $request->session()->flash('clear_storage');

        DB::commit();
        return redirect()->route('organizer.evaluations.edit', $params)->with('message', 'Evaluation Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();
        return redirect()->route('organizer.evaluations.index')->with('message', 'Evaluation Successfully removed');
    }

    /**
     * Retrieve the event resource using the provided code
     *
     * @param  $code
     * @return App\Model\Event
     */
    private function getEvent($code)
    {
        try {
            $event = Event::whereCode($code)->firstOrFail();
        }
        catch(ModelNotFoundException $e){
            DB::rollBack();
            abort(404);
        }

        return $event;
    }

    /**
     * Retrieves all the pending events based on the scuede_start column
     *
     * @param App\Model\Evaluation
     * @return App\Model\Evaluation
     */
    private function getPendingEvents($evaluation)
    {
        return $evaluation->events()->where('schedule_start', '>', Carbon::now());
    }
}
