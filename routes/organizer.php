<?php

//use App\Http\Controllers\Organizer\EventController;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::group([
    'middleware' => ['organizer', 'verified'],
], function(){
    Route::get('/', HomeController::class);

    Route::resource('events', EventController::class);
});