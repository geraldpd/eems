<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Attendee
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
            return redirect()->route('login');
        }

        $role_id = Auth::user()->roles->first()->id;

        if (in_array($role_id, [1, 2]))
        {
            return redirect()->route('/');
        }

        return $next($request);
    }
}
