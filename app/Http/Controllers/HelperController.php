<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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
            'suggestAttendees'
        ];
    }

    /**
     * search users table for for emails with similar keyword
     * @param string
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function suggestAttendees(Request $request)
    {
        $query = User::query()
            ->where(function($query) use ($request){
                return $query
                ->where('email', 'LIKE', "%{$request->keyword}%")
                ->orWhere(DB::raw("CONCAT(`firstname`, ' ', `lastname`)"), 'LIKE', '%' . $request->keyword . '%');
            })
            ->whereHas('roles', function($query) {
                return $query->where('name', 'attendee');
            })
            ->select(
                DB::raw("CONCAT(`firstname`, ' ', `lastname`) as name"),
                'email'
            )
            ->get()
            ->collect()
            ->transform(function($item) {
                return [
                    'value' => $item['email'],
                    'email' => $item['email'],
                    'name' => $item['name'],
                ];
            })
            ->toArray();

        return $query;
    }

}
