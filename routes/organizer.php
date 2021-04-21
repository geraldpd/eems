<?php

use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::group([
    'middleware' => ['organizer', 'verified'],
], function(){
    Route::get('/', HomeController::class);
});