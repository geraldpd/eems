<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::group([
    'middleware' => ['organizer'],
], function(){

    Route::resource('profile', ProfileController::class)->only(['index', 'update']);

    Route::group([
        'middleware' => ['verified'],
    ], function(){

        Route::get('/', HomeController::class);
        Route::resource('events/{event}/invitations', InvitationController::class)->only(['index', 'store']);
        Route::resource('events', EventController::class);
    });
});