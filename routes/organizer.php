<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::group([
    'middleware' => ['organizer', 'verified'],
], function(){
    Route::get('/', HomeController::class);

    Route::resource('profile', ProfileController::class)->only(['index', 'update']);

    Route::resource('events/{event}/invitations', InvitationController::class)->only(['index', 'store']);
    Route::resource('events', EventController::class);
});