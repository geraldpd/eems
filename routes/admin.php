<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController as User;

Auth::routes(['register' => false]);

Route::group([
    'middleware' => ['admin', 'verified'],
], function(){
    Route::get('/', HomeController::class);

    Route::resource('profile', ProfileController::class)->only(['index', 'update']);

    Route::get('users/attendees', [User::class, 'attendees'])->name('users.attendees');
    Route::get('users/organizers', [User::class, 'organizers'])->name('users.organizers');
    Route::resource('users', UserController::class);

    Route::resource('categories', CategoryController::class);

    Route::resource('types', TypeController::class);

    Route::resource('events', EventController::class);
});