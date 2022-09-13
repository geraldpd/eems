<?php

namespace App\Http\Controllers\Attendee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\EventServices;

class InvitationController extends Controller
{
    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $events = (new EventServices)->getEventsInvited()->get();

        return view('attendee.invitations.index', compact('events'));
    }
}
