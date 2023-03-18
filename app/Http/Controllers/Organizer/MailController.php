<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\MailRequest;
use App\Mail\ContactMailer;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
        $data = $request->validated();
        $path = public_path('email_uploads');
        $filepaths = [];
        $data['uploads'] = [];

        if($request->hasFile('attachments')) {

            foreach($request->attachments as $attachment) {
                $name = time().'.'.$attachment->getClientOriginalExtension();;

                if(!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                $attachment->move($path, $name);

                $filepaths[] = $path.'/'.$name;
            }

            $data['uploads'] = $filepaths;
        }

        Mail::to($request->email)->send(new ContactMailer($data));

        return  redirect()->back()->with('message', 'Email sent!');
    }
}
