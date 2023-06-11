<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Attendee\EventController as Event;

Route::group([
    'middleware' => ['attendee'], //'verified' // add verified middleware to methods/controllers/views where an attendee must verify their account first
], function () {

    Route::resource('profile', ProfileController::class)->only(['index', 'update']);

    Route::group([
        'middleware' => ['verified'],
    ], function () {

        Route::get('/', HomeController::class);

        Route::get('events/{event}/evaluation', [Event::class, 'evaluation'])->name('events.evaluation');
        Route::post('events/{event}/evaluate', [Event::class, 'evaluate'])->name('events.evaluate');
        Route::post('events/{event}/rate', [Event::class, 'rate'])->name('events.rate');
        Route::resource('events', EventController::class)->only(['index']);

        Route::resource('invitations', InvitationController::class)->only(['index']);
    });
});
