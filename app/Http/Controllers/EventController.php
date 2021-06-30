<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{

    public function index()
    {}

    public function invitation(Event $event, $email)
    {
        try {
            $email = decrypt($email);
        } catch (DecryptException $e) {
           return abort(404);
        }

        return view('front.event', compact('event'));
    }
}
