<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $events = Event::with('category')->get()->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->name,
                'start' => $event->schedule_start->format('Y-m-d'),
                'end' => $event->schedule_end->format('Y-m-d'),
                'event' => $event,
                'backgroundColor' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
            ];
        })
        ->groupBy(fn($item) => $item['event']->schedule_start->format('Y-m-d'));

        return view('admin.events.index', compact('events'));
    }
}