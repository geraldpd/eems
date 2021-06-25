<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Organizer\ {
    EventController as ControllerEvent
};


Auth::routes(['register' => false]);

Route::group([
    'middleware' => ['organizer', 'verified'],
], function(){
    Route::get('/', HomeController::class);

    Route::get('events/{event}/attendees', [ControllerEvent::class, 'attendees'])->name('events.attendees'); //? Manage Attendees
    Route::resource('events', EventController::class);
});