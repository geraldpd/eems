<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\HelperController;

use App\Http\Controllers\FrontController;
use App\Http\Controllers\EventController;
//use /HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes(['verify' => true]);

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/', [FrontController::class, 'index']);
Route::get('/home', [FrontController::class, 'index']);

Route::get('/events/{event}/{email}/invitation', [EventController::class, 'invitation'])->name('event.invitation');
Route::resource('events', EventController::class)->only(['show', 'index']);

//! THE FOLLOWING ROUTES ARE FOR HELPERS
Route::group([
    'prefix' =>'helpers',
    'as' =>'helpers.',
], function(){

    Route::get('/', [HelperController::class, 'index'])->name('index');
    Route::post('/suggest_attendees', [HelperController::class, 'suggestAttendees'])->name('suggest_attendees');

});