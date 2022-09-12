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
        $evaluations = Auth::user()->evaluations->map(function($evaluation) {
            $evaluation->events_count = $this->getPendingEvents($evaluation)->count();
            return $evaluation;
        });
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

        $evaluation->update([
            'questions' => "[{\"t6u88\":\"What do you think of this event?\"}]",
            'html_form' => '<li draggable="" data-type="text" class="form-group evaluation_entry alert alert-light">
                        <div class="row">
                            <div class="col-md-10 col-xs-12">
                                <label class="question_entry" data-question_key="t6u88" data-is_required="1">What do you think of this event? <strong class="text-danger" title="required">*</strong></label>
                            </div>
                            <div class="col-md-2 col-xs-12 d-flex justify-content-center">
                                <span class="edit-evaluation_type btn btn-link float-right">edit</span>
                                <span class="remove-evaluation_type btn btn-link text-secondary float-right">remove</span>
                            </div>
                            <div class="col-md-12"><textarea name="t6u88" class="form-control" placeholder="Your answer" minlength="0" maxlength="100" required="required"></textarea></div>
                        </div>
                    </li>'
        ]);

        if($request->has('event')) {
            $event = $this->getEvent($request->event);
            $event->update([
                'evaluation_id' => $evaluation->id,
                'evaluation_name' => $evaluation->name,
                'evaluation_description' => $evaluation->description,
                'evaluation_questions' => $evaluation->questions,
                'evaluation_html_form' => $evaluation->html_form
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

        $html_form = $evaluation->html_form;

        if($request->has('event')) {
            $event = $this->getEvent($request->event);

            $event->update([
                'evaluation_id' => $evaluation->id
            ]);

            if($event->evaluation_html_form) {
                $html_form = $event->evaluation_html_form;
            }
        }

        $evaluation->loadCount('events');
        $evaluation->pending_events = Event::pendingEvents()->where('evaluation_id', $evaluation->id)->get();

        return view('organizer.evaluations.edit', compact('evaluation', 'event', 'html_form'));
    }

    /*
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Evaluation $evaluation)
    {
        DB::beginTransaction();

        if($request->has('event')) { // this events evaluation only

            $event = $this->getEvent($request->event);

            if($event->dynamic_status == Event::CONCLUDED) {
                DB::rollback();
                return redirect()->back()->with('message', 'This event has already concluded, You can no longer moddify its evaluation sheet.');
            }

            $event->update([
                'evaluation_name' => $request->name,
                'evaluation_description' => $request->description,
                'evaluation_questions' => $request->questions,
                'evaluation_html_form' => $request->html_form
            ]);

            $params = [$evaluation->id, 'event' => $event->code];

        } else {

            switch ($request->update_type) {
                case 1: //update this one and the rest of the pending events using this evaluation

                    $evaluation->update($request->validated());

                    $this->getPendingEvents($evaluation)
                        ->update([
                            'evaluation_name' => $evaluation->name,
                            'evaluation_description' => $evaluation->description,
                            'evaluation_questions' => $evaluation->questions,
                            'evaluation_html_form' => $evaluation->html_form
                        ]);

                    break;

                default://update all
                    $evaluation->update($request->validated());
                    break;
            }

            $params = [$evaluation->id];
        }



        //if($this->getPendingEvents($evaluation)->count()){
        // if(false){

        //     $evaluation
        //     ->events()
        //     ->whereNot('code', '!=', $request->code)
        //     ->where('schedule_start', '>', Carbon::now()) //TODO add additional condition, i.e. events with staus = pending
        //     ->update([
        //         'evaluation_name' => $evaluation->name,
        //         'evaluation_description' => $evaluation->description,
        //         'evaluation_questions' => $evaluation->questions,
        //         'evaluation_html_form' => $evaluation->html_form
        //     ]);

        // }

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
        $evaluation->load('events');
        $pending_event_count = $this->getPendingEvents($evaluation)->count();

        if($pending_event_count) {
            return redirect()->back()->with('message', "$pending_event_count pending event(s) are attached to this evaluation. Please remove or transfer them to another evaluation sheet first.");
        }

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
        return Event::pendingEvents()->where('evaluation_id', $evaluation->id);
    }

    public function pendingEvents(Evaluation $evaluation)
    {
        return response()->json($this->getPendingEvents($evaluation)->get());
    }
}
