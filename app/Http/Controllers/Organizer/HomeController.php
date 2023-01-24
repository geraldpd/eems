<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Mail\ContactMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {

        #$data = [];
        #Mail::to('dasallagerald@gmail.com')->send(new ContactMailer($data));

        return view('organizer.index');
    }
}
