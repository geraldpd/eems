<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Mail\EventInvitation;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->indexTable();
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

    private function indexTable()
    {
        $events = Event::query()
        ->with(['organizer', 'category', 'type', 'schedules'])
        ->get();

        return view('admin.events.index.table', compact('events'));
    }

    public function show(Request $request, Event $event)
    {
        $preview = new EventInvitation($event, $event->organizer->email, $event->organizer->email);
        return view('admin.events.show', compact('event', 'preview'));
    }
}