<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Evaluation;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class EventEvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Event $event)
    {
        $event->loadCount('attendees');
        $event->load('evaluations.attendee');

        return view('organizer.events.evaluations.index', compact('event'));
    }

    /**
     * REUSE an existing event
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @param  \App\Models\Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event, Evaluation $evaluation)
    {
        DB::beginTransaction();

        if(!$evaluation->questions_array) {
            return redirect()->back()->with('message', 'The selected evaluation sheet has no entries. reuse of this sheet is not allowed');
        }

        $event->update([
            'evaluation_id' => $evaluation->id,
            'evaluation_name' => $evaluation->name,
            'evaluation_description' => $evaluation->description,
            'evaluation_questions' => $evaluation->questions,
            'evaluation_html_form' => $evaluation->html_form
        ]);

        DB::commit();

        return redirect()->route('organizer.events.evaluations.index', [$event->code])->with('message', $evaluation->name.' has been used as evaluation sheet.');

    }

    /**
     * REMOVE an evaluation sheet for an event
     *
     * @param  \App\Models\Event  $event
     * @param  \App\Models\Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event, Evaluation $evaluation)
    {
        $event->update([
            'evaluation_id' => null,
            'evaluation_name' => null,
            'evaluation_description' => null,
            'evaluation_questions' => null,
            'evaluation_html_form' => null,
        ]);

        return redirect()->route('organizer.events.evaluations.index', [$event->code])->with('message', 'Evaluation Successfully removed');
    }

    public function download(Request $request, Event $event)
    {
        $event->load('evaluations.attendee');
        $questions = collect($event->evaluation_questions)->flatMap(fn($values) => $values);

        $data = collect($event->evaluations)->mapWithKeys(function($evalaution) use ($questions) {
            $feedback = $evalaution->feedback;
            $processed_feeback = $questions->mapWithKeys(function($question, $key) use ($feedback){
                if(array_key_exists($key, $feedback)) {
                    return [$question => $feedback[$key]];
                } else {
                    return [$question => ''];
                }
            })->all();

            return [$evalaution->attendee->email => $processed_feeback];
        });

        $event->downloadable_filename = Carbon::now()->format('y-m-d').' - '.$event->name.' Evaluations';

       $file = $request->as == 'JSON'
        ? $this->asJSON($data, $event)
        : $this->asCSV($data, $event, $questions);

       return response()->download($file);
    }

    private function asJSON($data, $event)
    {
        file_put_contents($event->downloadable_filename.'.json', $data->toJson(JSON_PRETTY_PRINT));

        return public_path($event->downloadable_filename.'.json');
    }

    private function asCSV($data, $event, $questions)
    {
        $filename = $event->downloadable_filename.'.csv';
        $handle = fopen($filename, 'w');

        $headers = array_values($questions->all());
        array_unshift($headers, 'Attendee');

        fputcsv($handle, $headers);

        foreach ($data as $attendee => $feedback) {
            $evaluation = array_values($feedback);
            array_unshift($evaluation, $attendee);

            $evaluation = collect($evaluation)->map(function($item) {
                if(gettype($item) === 'string') {
                    return $item;
                } else {
                    return collect($item)->join(', ');
                }
            })->all();

            fputcsv($handle, $evaluation);
        }

        fclose($handle);

        return public_path($filename);
    }

}