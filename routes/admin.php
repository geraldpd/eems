<?php

use Illuminate\Support\Facades\Route;

use Admin\HomeController;

Auth::routes(['register' => false]);

Route::group([
    'middleware'=>'auth',
], function(){
    Route::get('/home', [HomeController::class])->name('home');
});