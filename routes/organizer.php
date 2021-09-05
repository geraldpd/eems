<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\organizer\TemporaryDocumentController as TemporaryDocumentController;
use App\Http\Controllers\organizer\EventEvaluationController as EventEvaluation;

Route::group([
    'middleware' => ['organizer'],
], function(){

    Route::resource('profile', ProfileController::class)->only(['index', 'update']);

    Route::group([
        'middleware' => ['verified'],
    ], function(){

        Route::get('/', HomeController::class);

        //EVALUATIONS
        Route::resource('evaluations', EvaluationController::class);

        //TEMPORARY DOCUMENTS
        Route::POST('events/temp-docs', [TemporaryDocumentController::class, 'store'])->name('tempdocs.store');
        Route::DELETE('events/temp-docs', [TemporaryDocumentController::class, 'destroy'])->name('tempdocs.destroy');

        Route::get('events/{event}/evaluations/download', [EventEvaluation::class, 'download'])->name('events.evaluations.download');

        //EVENTS
        Route::resource('events', EventController::class);

        //EVENT INVITATIONS
        Route::resource('events/{event}/invitations', InvitationController::class)->only(['index', 'store']);

        //EVENT EVALUATIONS
        Route::resource('events/{event}/evaluations', EventEvaluationController::class, ['as' => 'events']);


    });
});