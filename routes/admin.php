<?php

use Illuminate\Support\Facades\Route;

//use Admin\HomeController;

Auth::routes(['register' => false]);

Route::group( ['middleware' => 'admin'], function() {
    Route::get('/', HomeController::class);
    Route::get('/home', HomeController::class);
});