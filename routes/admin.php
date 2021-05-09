<?php

use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::group([
    'middleware' => ['admin', 'verified'],
], function(){
    Route::get('/', HomeController::class);

    Route::resource('users', UserController::class);
});