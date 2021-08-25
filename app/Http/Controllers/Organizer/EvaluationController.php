<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Evaluation\StoreRequest;
use App\Http\Requests\Evaluation\UpdateRequest;
use App\Models\Evaluation;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class EvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $evaluations = Auth::user()->evaluations;
        return view('organizer.evaluations.index', compact('evaluations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            $event = $request->has('event') ? Event::whereCode($request->event)->firstOrFail() : null;
        }
        catch(ModelNotFoundException $e){
            abort(404);
        }

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
        $evaluation = Auth::user()->evaluations()->create($request->validated());
        $params = [$evaluation->id];

        if($request->has('event')) {
            $event = Event::whereCode($request->event)->first();

            $event->update([
                'evaluation_id' => $evaluation->id
            ]);

            $params = [$evaluation->id, 'event' => $event->code];
        }

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
            $event = Event::whereCode($request->event)->first();

            $event->update([
                'evaluation_id' => $evaluation->id
            ]);
        }

        $evaluation->loadCount('events');

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
        $evaluation->update($request->validated());
        $request->session()->flash('clear_storage');
        $params = [$evaluation->id];

        if($request->has('event')) {
            $event = Event::whereCode($request->event)->first();

            $event->update([
                'evaluation_questions' => $evaluation->questions
            ]);

            $params = [$evaluation->id, 'event' => $event->code];
        }

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
}
