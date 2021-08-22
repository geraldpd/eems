<?php
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['organizer'],
], function(){

    Route::resource('profile', ProfileController::class)->only(['index', 'update']);

    Route::group([
        'middleware' => ['verified'],
    ], function(){

        Route::get('/', HomeController::class);

        Route::resource('events', EventController::class);
        Route::resource('events/{event}/invitations', InvitationController::class)->only(['index', 'store']);

        Route::resource('evaluations', EvaluationController::class);
    });
});