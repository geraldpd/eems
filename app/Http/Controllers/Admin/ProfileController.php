<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.profile.index');
    }

    public function update(Request $request)
    {
        dd($request->all());
    }

}
