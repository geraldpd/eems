<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\StoreRequest;
use App\Http\Requests\Event\UpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Event;
use Carbon\Carbon;

use App\Mail\EventInvitation;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Auth::user()->organizedEvents()->with('category')->get()->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->name,
                'start' => $event->schedule_start->format('Y-m-d'),
                'end' => $event->schedule_end->format('Y-m-d'),
                'event' => $event,
                'backgroundColor' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
            ];
        })
        ->groupBy(function ($item, $key) {
            return $item['event']->schedule_start->format('Y-m-d');
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
        $date_range = explode(' to ', $request->date);
        $date = $request->date ? Carbon::parse($request->date) : Carbon::now();

        if(count($date_range) > 1) {
            return redirect()->route('organizer.events.index')->with('message', 'Multi date event creation coming soon!');
        }

        if($date->copy()->startOfDay() < Carbon::now()->startOfDay()) {
            return redirect()->route('organizer.events.index')->with('message', 'Cannot add events on past dates');
        }

        $is_same_day = $date->copy()->startOfDay() == Carbon::now()->startOfDay(); //? check if the selected day is the same as the current date
        $default_event_min_time =  config('eems.default_event_min_time');
        $categories = Category::all();
        $min_sched = [
            'start' => $is_same_day ? Carbon::now()->addHour()->toTimeString() : $default_event_min_time['start'],
            'end' => $is_same_day ? Carbon::now()->addHours(2)->toTimeString() : $default_event_min_time['end']
        ];

        return view('organizer.events.create', compact('categories', 'date', 'min_sched'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $data = collect($request->validated());

        //TODO assuming that the date picked is single day
        $schedule_start = Carbon::parse($request->schedule_start); //refers to the TIME only not the date
        $schedule_end = Carbon::parse($request->schedule_end); //refers to the TIME only not the date
        $date = Carbon::parse($request->date);

        $event_data = $data->merge([
            'organizer_id' => Auth::user()->id,
            'schedule_start' => $date->copy()->setHour($schedule_start->hour)->setMinute($schedule_start->minute),
            'schedule_end' => $date->copy()->setHour($schedule_end->hour)->setMinute($schedule_end->minute),
            'status' => Event::Pending,
        ]);

        Event::create($event_data->all());

        return redirect()->route('organizer.events.index')->with('message', 'Event Successfully Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        $preview = new EventInvitation($event, true);

        return view('organizer.events.show', compact('event', 'preview'));
    }

    /**
     * Manage attendees of the resource
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function attendees(Event $event)
    {
        //Mail::to('admin@laravel.com')->send(new EventInvitation($event));

        $event->load(['attendees']);

        //dd($event);
        return view('organizer.events.attendees', compact('event'));
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
        //disable editing events that has passed its scheduled date
        if($event->schedule_start->isPast()) {

            return redirect()->route('organizer.events.index')->with('message', "Event $event->name has passed its scheduled date, editing is no longer allowed.");

        }

        //disable editing events that is almost about to start
        if($event->schedule_start < Carbon::now()->addHour()) {

            return redirect()->route('organizer.events.index')->with('message', "Event $event->name is about to start, editing the event is no longer allowed.");

        }

        $categories = Category::all();
        $event->load('category');

        return view('organizer.events.edit', compact('event','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Event $event)
    {
        //disable editing events that is almost about to start
        if($event->schedule_start < Carbon::now()->addHour()) {
            return redirect()->route('organizer.events.index')->with('message', "Event $event->name is about to start, editing the event is no longer allowed.");
        }

        if($event->organizer_id != Auth::user()->id) {
            return redirect()->route('organizer.events.index')->with('message', "You don't seem to be the organizer for the $event->name event, updating it is not allowed.");
        }

        $event->update($request->validated());

        return redirect()->route('organizer.events.index')->with('message', 'Event Successfully Updated');
    }

    //? RESCHEDULE METHOD
    /*
        public function reschedule(Event $event)
        {
            //
        }
    */

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
