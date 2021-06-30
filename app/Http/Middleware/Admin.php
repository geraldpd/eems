<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
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
            return redirect()->route('admin.login');
        }

        $role_id = Auth::user()->roles->first()->id;

        if (in_array($role_id, [2, 3]))
        {
            $index = Auth::user()->roles->first()->id - 1;
            return redirect()->route(config('eems.roles')[$index].".");
            //return redirect()->route('/admin');
        }

        return $next($request);
    }
}
