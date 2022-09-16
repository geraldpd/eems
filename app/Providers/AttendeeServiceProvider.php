<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use App\Services\EventServices;
use Illuminate\Support\Facades\View;

class AttendeeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.auth.attendee', function ($view) {
            $invitations = (new EventServices)->getEventsInvited()->count();
            $view->with('my_invitations', $invitations);
        });
    }
}
