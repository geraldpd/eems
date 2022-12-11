<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelperController;

use App\Http\Controllers\FrontController;
use App\Http\Controllers\EventController as Event;

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

Route::get('/', [FrontController::class, 'index']);
Route::get('/home', [FrontController::class, 'index']);
Route::get('/about', [FrontController::class, 'about'])->name('about');
Route::get('/news', [FrontController::class, 'news'])->name('news');


Route::post('/events/{event}/book', [Event::class, 'book'])->name('event.book');
Route::post('/events/{event}/book/accept_invitation', [Event::class, 'acceptBookingInvitation'])->name('event.accept_booking_invitation');
Route::get('/events/{event}/{email}/invitation', [Event::class, 'invitation'])->name('event.invitation');
Route::resource('events', EventController::class)->only(['show', 'index']);

//! THE FOLLOWING ROUTES ARE FOR HELPERS
Route::group([
    'prefix' =>'helpers',
    'as' =>'helpers.',
], function(){

    Route::get('/', [HelperController::class, 'index'])->name('index');
    Route::get('/download-event-attachment', [HelperController::class, 'downloadEventAttachment'])->name('download-event-attachment');
    Route::post('/suggest_attendees', [HelperController::class, 'suggestAttendees'])->name('suggest_attendees');

});