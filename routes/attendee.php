<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['attendee'], //'verified' // add verified middleware to methods/controllers/views where an attendee must verify thier account first
], function(){

    Route::resource('profile', ProfileController::class)->only(['index', 'update']);

    Route::group([
        'middleware' => ['verified'],
    ], function(){

        Route::get('/', HomeController::class);
        Route::resource('events', EventController::class);
    });
});