<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use App\Mail\EventInvitation;
use App\Models\Category;
use App\Models\Type;
use App\Models\Event;
use App\Http\Requests\Event\StoreRequest;
use App\Http\Requests\Event\UpdateRequest;

use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $background_color = [
            'PENDING' => '#007bff',
            'ONGOING' => '#28a745',
            'CONCLUDED' => '#6c757d',
        ];

        $events = Auth::user()->organizedEvents()->with('category')->get()->map(function($event) use ($background_color) {
            return [
                'id' => $event->id,
                'title' => $event->name,
                'start' => $event->schedule_start->format('Y-m-d'),
                'end' => $event->schedule_end->format('Y-m-d'),
                'event' => $event,
                //'backgroundColor' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
                'backgroundColor' => $background_color[eventHelperGetDynamicStatus($event)],
            ];
        })
        ->groupBy(function ($item, $key) {
            $start = $item['event']->schedule_start->format('Y-m-d');
            $end = $item['event']->schedule_end->format('Y-m-d');
            return "$start,$end";
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
        $default_event_min_time = config('eems.default_event_min_time');

        $categories = Category::whereIsActive(true)->get();
        $types = Type::whereIsActive(true)->get();

        $min_sched = [
            'start' => $is_same_day ? Carbon::now()->addHour()->toTimeString() : $default_event_min_time['start'],
            'end' => $is_same_day ? Carbon::now()->addHours(2)->toTimeString() : $default_event_min_time['end']
        ];

        $user = Auth::user();
        $event_folder_path = "storage/users/organizers/$user->id/temp_docs"; // temp docs for uploading event files

        if(!File::exists($event_folder_path)) {
            File::makeDirectory($event_folder_path, 0777, true);
        }

        $documents = $this->getTemporayDocs();

        //dd($documents);
        return view('organizer.events.create', compact('types', 'categories', 'date', 'min_sched', 'documents'));
    }

    public function createMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => 'required',
            'end' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('organizer.events.index')->with('message', 'Something went wrong, please select proper dates!');
        }

        $period = CarbonPeriod::create($request->start, $request->end);
        $categories = Category::whereIsActive(true)->get();
        $types = Type::whereIsActive(true)->get();
        $documents = [];

        return view('organizer.events.create_multiple', compact('types', 'categories', 'documents', 'period'));
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

        //dd($event_data->all());
        $event = Event::create($event_data->all());

        $event->code = eventHelperSetCode($event->id);
        $event_folder_path = "storage/events/$event->id/";
        $qrcode_invitation_link = route('events.show', $event->code).'?invite=true';

        File::makeDirectory($event_folder_path);
        QrCode::generate($qrcode_invitation_link, $event_folder_path.'qrcode.svg');
        $event->qrcode = $event_folder_path.'qrcode.svg';

        $event->save();

        File::makeDirectory($event_folder_path.'documents/');
        $this->moveTemporayDocsToEvents($event_folder_path);

        DB::commit();

        return redirect()->route('organizer.events.show', [$event->code])->with('message', 'Event Successfully Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        $preview = new EventInvitation($event, Auth::user()->email, Auth::user()->email);
        return view('organizer.events.show', compact('event', 'preview'));
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

        $event->documents = $event->uploaded_documents;
        $event->temporary_documents = $this->getTemporayDocs();

        return view('organizer.events.edit', compact('event','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event´
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {

        try {
            DB::beginTransaction();

            //disable editing events that is almost about to start
            if($event->schedule_start < Carbon::now()->addHour()) {
                return redirect()->route('organizer.events.index')->with('message', "Event $event->name is about to start, editing the event is no longer allowed.");
            }

            if($event->organizer_id != Auth::user()->id) {
                return redirect()->route('organizer.events.index')->with('message', "You don't seem to be the organizer for the $event->name event, updating it is not allowed.");
            }

            $event->update($request->all());

            $event_folder_path = "storage/events/$event->id/";
            $this->moveTemporayDocsToEvents($event_folder_path);

            DB::commit();
            return redirect()->route('organizer.events.show', [$event->code])->with('message', 'Event Successfully Updated');

        } catch (\Throwable $th) {
            throw $th;
        }

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

    private function getTemporayDocs()
    {
        $temporary_document_path = "storage/users/organizers/".Auth::user()->id."/temp_docs";
        $documents = File::allFiles($temporary_document_path);

        return collect($documents)
        ->sortBy(function ($file) {
            return $file->getCTime();
        })
        ->mapWithKeys(function ($file) {
            return [$file->getBaseName() => [
                'public' => $file->getRealPath(),
                'asset' => asset($file->getPathName())
            ]];
        })
        ->all();
    }

    private function moveTemporayDocsToEvents($event_folder_path)
    {
        //get temp files
        $documents = $this->getTemporayDocs();

        foreach($documents as $name => $path) {
            File::move($path['public'], public_path($event_folder_path.'documents/'.$name));
        }
    }

}
