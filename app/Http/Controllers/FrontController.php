<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\EventServices;

class FrontController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $events = (new EventServices())->getFrontEndEventsPerDay([
        //     'keyword'           => false,
        //     'exclude_concluded' => true,
        //     'has_attended'      => false
        // ])->get()->all();

        $events = (new EventServices())
            ->getFrontEndEvents([
                'keyword'           => false,
                'exclude_concluded' => true,
                'has_attended'      => false,
                'order'             => 'desc',
            ])
            ->limit(9)
            ->get();

        $topOrganizers = User::withCount('organizedEvents')
            ->with('organization')
            ->orderBy('organized_events_count', 'desc')
            ->take(3)
            ->get();

        $topAttendees = User::withCount('attendedEvents')
            ->orderBy('attended_events_count', 'desc')
            ->take(3)
            ->get();

        return view('front.welcome', compact('events', 'topOrganizers', 'topAttendees'));
    }

    public function home()
    {
        return $this->index();
    }

    public function about()
    {
        return view('front.about');
    }

    public function news()
    {
        return view('front.news');
    }
}
