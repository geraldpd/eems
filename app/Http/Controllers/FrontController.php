<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
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
            'has_attended'      => false
        ])
        ->paginate(15);

        return view('front.welcome', compact('events'));
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
