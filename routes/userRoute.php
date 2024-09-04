<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/home-user', 'home_user.index')
    ->name("home_user") 
    ->middleware(['auth','user']);

Volt::route('/meus-boletos', 'clientes_boletos.index')
    ->name("meusboletos")
    ->middleware(['auth','user']);