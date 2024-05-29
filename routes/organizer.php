<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Organizer\TemporaryDocumentController as TemporaryDocumentController;
use App\Http\Controllers\Organizer\EventEvaluationController as EventEvaluation;
use App\Http\Controllers\Organizer\InvitationController as Invitation;
use App\Http\Controllers\Organizer\EventController as Event;
use App\Http\Controllers\Organizer\EvaluationController as Evaluation;
use App\Http\Controllers\Organizer\MailController as Mail;
use App\Http\Controllers\Organizer\OrganizationController as Organization;

Route::group([
    'middleware' => ['organizer'],
], function () {

    Route::post('profile/supporting_documents', [Organization::class, 'storeDocuments'])->name('profile.supporting_documents.create');
    Route::get('profile/supporting_documents/{supporting_document}', [Organization::class, 'getDocuments'])->name('profile.supporting_documents.download');
    Route::delete('profile/supporting_documents/{supporting_document}', [Organization::class, 'deleteDocuments'])->name('profile.supporting_documents.delete');
    Route::resource('profile', ProfileController::class)->only(['index', 'update']);

    Route::group([
        'middleware' => ['verified'],
    ], function () {

        Route::get('/', HomeController::class);

        //EVALUATIONS
        Route::get('evaluations/{evaluation}/pending-events', [Evaluation::class, 'pendingEvents'])->name('evaluations.pending-events');
        Route::resource('evaluations', EvaluationController::class);

        //TEMPORARY DOCUMENTS
        Route::get('events/temp-docs', [TemporaryDocumentController::class, 'retrieve'])->name('tempdocs.retrieve');
        Route::post('events/temp-docs', [TemporaryDocumentController::class, 'store'])->name('tempdocs.store');
        Route::delete('events/temp-docs', [TemporaryDocumentController::class, 'destroy'])->name('tempdocs.destroy');

        //EVENTS
        Route::get('events/fetch-scheduled-events', [Event::class, 'fetchScheduleEvents'])->name('events.fetch-scheduled-events');
        Route::resource('events', EventController::class);

        //EVENT INVITATIONS
        Route::post('events/{event}/invitations/approve-book', [Invitation::class, 'approveBooking'])->name('invitations.approveBooking');
        Route::post('events/{event}/invitations/disapprove-book', [Invitation::class, 'disapproveBooking'])->name('invitations.disapproveBooking');
        Route::get('events/{event}/invitations/{filter?}', [Invitation::class, 'index'])->name('invitations.index');
        Route::get('events/{event}/invitations/print/{filter?}', [Invitation::class, 'print'])->name('invitations.print');
        Route::get('events/{event}/invitations/{filter}/download', [Invitation::class, 'download'])->name('invitations.download');
        Route::post('events/{event}/invitations', [Invitation::class, 'store'])->name('invitations.store');
        //Route::resource('events/{event}/invitations', InvitationController::class)->only(['index', 'store']);

        //EVENT EVALUATIONS
        Route::get('events/{event}/evaluations/download', [EventEvaluation::class, 'download'])->name('events.evaluations.download');
        Route::post('events/{event}/evaluations/close-open', [EventEvaluation::class, 'close_open'])->name('events.evalautions.close-open');
        Route::resource('events/{event}/evaluations', EventEvaluationController::class, ['as' => 'events']);

        Route::resource('mails', MailController::class)->only(['index']);
        Route::post('mails/send', [Mail::class, 'send'])->name('mails.send');
    });
});
