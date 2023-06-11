<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Services\EventServices;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HelperController extends Controller
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

    public function index()
    {
        return [
            'suggestAttendees',
            'downloadFile'
        ];
    }

    /**
     * search attendees from users table for for emails with similar keyword
     * @param string
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function suggestAttendees(Request $request)
    {
        $query = User::query()
            ->where(function ($query) use ($request) {
                return $query
                    ->where('email', 'LIKE', "%{$request->keyword}%")
                    ->orWhere(DB::raw("CONCAT(`firstname`, ' ', `lastname`)"), 'LIKE', '%' . $request->keyword . '%');
            })
            ->whereHas('roles', function ($query) {
                return $query->where('name', 'attendee');
            })
            ->when($request->has('event_id'), function ($query) {
                $event = Event::find(request()->event_id)->load('invitations');
                $invited_emails = $event->invitations->pluck('email');

                return $query->whereNotIn('email', $invited_emails);
            })
            ->select(
                DB::raw("CONCAT(`firstname`, ' ', `lastname`) as name"),
                'email'
            )
            ->get()
            ->collect()
            ->transform(function ($item) {
                return [
                    'value' => $item['email'],
                    'email' => $item['email'],
                    'name' => $item['name'],
                ];
            })
            ->toArray();

        return $query;
    }

    public function downloadFile(Request $request)
    {
        return (new EventServices)->downloadAttachment($request->document);
    }
}
