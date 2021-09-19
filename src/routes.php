<?php

use Illuminate\Support\Facades\Route;
use JimmyHoweDotCom\SingleSignOn\Http\Controllers\SingleSignOnController;

Route::prefix('auth')->middleware(['web'])->group(function ()
{
    Route::name('sso.login')
         ->get('/sso/login', [
             SingleSignOnController::class,
             'login',
         ]);

    Route::name('sso.callback')
         ->get('/callback', [
             SingleSignOnController::class,
             'callback',
         ]);

    Route::name('sso.connect')
         ->get('/sso/connect', [
             SingleSignOnController::class,
             'connect',
         ]);

    Route::name('sso.disconnect')
         ->get('/sso/disconnect', [
             SingleSignOnController::class,
             'disconnect',
         ]);
});
