<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Organizer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check())
        {
            return redirect()->route('organizer.login');
        }

        if (in_array(Auth::user()->roles->first()->id, [1, 3]))
        {
            $index = Auth::user()->roles->first()->id - 1;
            return redirect()->route(config('eems.roles')[$index].".");
        }

        return $next($request);
    }
}
