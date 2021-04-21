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

        $role_id = Auth::user()->roles->first()->id;

        if (in_array($role_id, [1, 3]))
        {
            return redirect()->route('/organizer');
        }

        return $next($request);
    }
}
