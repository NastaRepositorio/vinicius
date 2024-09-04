<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
});

Volt::route('/login', 'auth.login')
    ->name('login');

require __DIR__ . '/adminRoute.php';
require __DIR__ . '/userRoute.php';
require __DIR__ . '/sellerRoute.php';


