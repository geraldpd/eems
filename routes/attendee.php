<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['attendee', 'verified'],
], function(){
    Route::get('/', HomeController::class);

    Route::resource('profile', ProfileController::class)->only(['index', 'update']);

    Route::resource('events', EventController::class);
});