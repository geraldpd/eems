<?php

namespace App\Http\Controllers\Attendee;

use App\Http\Controllers\Controller;
use App\Services\EventServices;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $upcommingEvents = (new EventServices())
            ->getFrontEndEvents([
                'keyword'           => request()->filled('keyword') ? request()->keyword : false,
                'exclude_concluded' => true,
                'has_attended'      => false,
                'order'             => 'asc'
            ])
            ->paginate(12);

        return view('attendee.index', compact('upcommingEvents'));
    }
}
