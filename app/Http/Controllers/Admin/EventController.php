<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Mail\EventInvitation;
use App\Models\category;
use App\Models\Type;
use App\Models\User;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->indexTable($request);
    }

    private function indexCalendar()
    {
        $events = Event::query()
        ->with(['category', 'type'])
        ->get()
        ->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->name,
                'start' => $event->start->schedule_start->format('Y-m-d H:i'),
                'end' => $event->end->schedule_end->format('Y-m-d H:i'),
                'code' => $event->code,
            ];
        });

        return view('admin.events.index.calendar', compact('events'));
    }

    private function indexTable(Request $request)
    {

        if($request->has('organizer')) {
            $organizer = User::where('email', request()->organizer)->firstOrFail();

            if(! $organizer->hasRole('organizer')) {
                return redirect()->back()->with('message', 'Organizer could not be found');
            }
        }

        $events = Event::query()
        ->with(['organizer', 'category', 'type', 'schedules'])
        ->when($request->has('organizer'), function($query) {
            $query->whereRelation('organizer', 'email', request()->organizer);
        })
        ->when($request->has('type'), function($query) {
            $query->whereRelation('type', 'id', request()->type);
        })
        ->when($request->has('category'), function($query) {
            $query->whereRelation('category', 'id', request()->category);
        })
        ->get();

        $filter = false;
        switch (true) {
            case $request->has('type'):
                $filter = Type::findOrFail($request->type)->name;
                break;
            case $request->has('category'):
                $filter = Category::findOrFail($request->category)->name;
                break;
        }
        return view('admin.events.index.table', compact('events', 'filter'));
    }

    public function show(Request $request, Event $event)
    {
        $preview = new EventInvitation($event, $event->organizer->email, $event->organizer->email);
        return view('admin.events.show', compact('event', 'preview'));
    }
}