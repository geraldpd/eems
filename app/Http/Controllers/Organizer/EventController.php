<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Models\Category;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            $events = Auth::user()->events->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->name,
                    'start' => $event->schedule_start,
                    'end' => $event->schedule_end,
                    'event' => $event
                ];
            });

            return view('organizer.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $categories = Category::all();
        $date = $request->date ? Carbon::parse($request->date) : Carbon::now();

        return view('organizer.events.create', compact('categories', 'date'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventRequest $request)
    {
        $data = collect($request->validated());

        //TODO assuming that the date picked is single day
        $schedule_start = Carbon::parse($request->schedule_start);
        $schedule_end = Carbon::parse($request->schedule_end);
        $date = Carbon::parse($request->date);

        $event_data = $data->merge([
            'organizer_id' => Auth::user()->id,
            'schedule_start' => $date->copy()->setHour($schedule_start->hour)->setMinute($schedule_start->minute),
            'schedule_end' => $date->copy()->setHour($schedule_end->hour)->setMinute($schedule_end->minute),
            'status' => Event::Pending,
        ]);

        Event::create($event_data->all());

        return redirect()->route('organizer.events.index')->with('success', 'Event Successfully Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Display all resource of the selected date.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }
}
