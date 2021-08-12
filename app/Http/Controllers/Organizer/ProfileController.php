<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return view('organizer.profile.index');
    }

    public function update(Request $request)
    {
        dd($request->all());
    }

}