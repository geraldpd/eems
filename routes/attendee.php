<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['attendee', 'verified'],
], function(){
    Route::get('/', HomeController::class);
});