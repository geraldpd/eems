<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\MailRequest;
use App\Mail\ContactMailer;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $events = Auth::user()->organizedEvents()->select('id')->get();

        return view('organizer.mails.index', compact('events'));
    }

    public function send(MailRequest $request)
    {
        Mail::to($request->email)->send(new ContactMailer($request->validated()));

        return  redirect()->back()->with('message', 'Email sent!');
    }
}
