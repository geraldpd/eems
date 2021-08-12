<?php

namespace App\Http\Controllers\Attendee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        rreturn view('attendee.profile.index');
    }

    public function update(Request $request)
    {
        dd($request->all());
    }

}
