<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\organizer\TemporaryDocumentController as TemporaryDocumentController;

Route::group([
    'middleware' => ['organizer'],
], function(){

    Route::resource('profile', ProfileController::class)->only(['index', 'update']);

    Route::group([
        'middleware' => ['verified'],
    ], function(){

        Route::get('/', HomeController::class);

        Route::resource('evaluations', EvaluationController::class);

        Route::POST('events/temp-docs', [TemporaryDocumentController::class, 'store'])->name('tempdocs.store');
        Route::DELETE('events/temp-docs', [TemporaryDocumentController::class, 'destroy'])->name('tempdocs.destroy');
        Route::resource('events', EventController::class);
        Route::resource('events/{event}/invitations', InvitationController::class)->only(['index', 'store']);
        Route::resource('events/{event}/evaluations', EventEvaluationController::class, ['as' => 'events']);

    });
});