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
use App\Models\EventSchedule;
use App\Http\Requests\Event\{
    StoreRequest,
    UpdateRequest
};
use Carbon\Exceptions\InvalidFormatException;
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
        $events = Auth::user()->organizedEvents()
        ->with(['category', 'type'])
        ->get()
        ->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->name,
                'start' => $event->schedules->first()->schedule_start->format('Y-m-d H:i'),
                'end' => $event->schedules->last()->schedule_end->format('Y-m-d H:i'),
                'code' => $event->code,
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
        $validator = Validator::make($request->all(), [
            'start' => 'required',
            'end' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('organizer.events.index')->with('message', 'Something went wrong, please select proper dates!');
        }

        try {
            $start = @Carbon::parse($request->start);
            $end = @Carbon::parse($request->end);
        } catch (InvalidFormatException $_) {
            return redirect()->route('organizer.events.index')->with('message', 'Something went wrong, please select proper dates!');
        }

        $today = Carbon::now()->format('Y-m-d');
        $period = CarbonPeriod::create($request->start, $request->end)->toArray();

        if($start->copy()->startOfDay() < Carbon::now()->startOfDay()) {
            return redirect()->route('organizer.events.index')->with('message', 'Cannot add events on past dates');
        }

        $default_event_min_time = config('eems.default_event_min_time');

        $min_sched = [
            'start' => $default_event_min_time['start'],
            'end' => $default_event_min_time['end']
        ];

        $user = Auth::user();
        $event_folder_path = "storage/users/organizers/$user->id/temp_docs"; // temp docs for uploading event files

        if(!File::exists($event_folder_path)) {
            File::makeDirectory($event_folder_path, 0777, true);
        }

        $documents = $this->getTemporayDocs();
        $categories = Category::whereIsActive(true)->get();
        $types = Type::whereIsActive(true)->get();

        return view('organizer.events.create', compact('period', 'types', 'categories', 'min_sched', 'documents'));
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

        //* all fields
        $data = collect($request->validated());

        //*organier_id and status
        $event_data = $data->merge([
            'organizer_id' => Auth::user()->id,
            'status' => Event::Pending,
        ]);

        $event = Event::create($event_data->all());

        //*code
        $event->code = eventHelperSetCode($event->id);

        //* qrcode
        $event_folder_path = "storage/events/$event->id/";
        $qrcode_invitation_link = route('events.show', $event->code).'?invite=true';
        File::makeDirectory($event_folder_path);
        QrCode::generate($qrcode_invitation_link, $event_folder_path.'qrcode.svg');
        $event->qrcode = $event_folder_path.'qrcode.svg';

        $event->save();

        //*event schedules
        $event_schedule = [];
        foreach($request->schedules as $date => $schedule) {

            $schedule_start = Carbon::parse($schedule['start']);
            $schedule_end = Carbon::parse($schedule['end']);

            $event_schedule[] = [
                'event_id' => $event->id,
                'schedule_start' => Carbon::parse($date)->setHour($schedule_start->hour)->setMinute($schedule_start->minute)->format('y-m-d H:i:s'),
                'schedule_end' => Carbon::parse($date)->setHour($schedule_end->hour)->setMinute($schedule_end->minute)->format('y-m-d H:i:s'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        EventSchedule::insert($event_schedule);

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
        $event->load('schedules');
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
        $default_event_min_time = config('eems.default_event_min_time');
        $min_sched = [
            'start' => $default_event_min_time['start'],
            'end' => $default_event_min_time['end']
        ];

        $types = Type::whereIsActive(true)->get();
        $categories = Category::all();

        $event->load('category');

        $event->documents = $event->uploaded_documents;
        $event->temporary_documents = $this->getTemporayDocs();

        return view('organizer.events.edit', compact('event','categories', 'types', 'min_sched'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event´
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Event $event)
    {
        try {
            DB::beginTransaction();

            //disable editing events that is almost about to start
            // if($event->schedule_start < Carbon::now()->addHour()) {
            //     return redirect()->route('organizer.events.index')->with('message', "Event $event->name is about to start, editing the event is no longer allowed.");
            // }

            // if($event->organizer_id != Auth::user()->id) {
            //     return redirect()->route('organizer.events.index')->with('message', "You don't seem to be the organizer for the $event->name event, updating it is not allowed.");
            // }

            $event->update($request->validated());

            $event_schedule = [];
            foreach($request->schedules as $schedule_id => $schedule) {
                $schedule_start = Carbon::parse($schedule['schedule_start']);
                $schedule_end = Carbon::parse($schedule['schedule_end']);

                $event_schedule = EventSchedule::find($schedule_id);
                $event_schedule->schedule_start = Carbon::parse($event_schedule->schedule_start)->setHour($schedule_start->hour)->setMinute($schedule_start->minute)->format('y-m-d H:i:s');
                $event_schedule->schedule_end = Carbon::parse($event_schedule->schedule_end)->setHour($schedule_end->hour)->setMinute($schedule_end->minute)->format('y-m-d H:i:s');
                $event_schedule->save();
            }

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

    public function fetchScheduleEvents(Request $request)
    {
        $scheduled_events = EventSchedule::query()
        ->with(['event.category', 'event.type'])
        ->whereDate('schedule_start', '>=', Carbon::parse($request->start)->startOfDay())
        ->whereDate('schedule_end', '<=', Carbon::parse($request->end)->endOfDay())
        ->orderBy('schedule_start')
        ->get()
        ->mapToGroups(function ($item, $key) {
            return [Carbon::parse($item['schedule_start'])->format('Y-m-d') => $item];
        });

        return response($scheduled_events);
    }
}
