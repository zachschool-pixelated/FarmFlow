<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-nav', function () {
    // create a fake user so Auth::user() doesn't fail
    $user = \App\Models\User::first();
    auth()->login($user);
    return view('layouts.navigation');
});
