<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/home', 'home.index')
    ->name('home')
    ->middleware(['auth', 'admin']);

Volt::route('/users', 'users.index')
    ->name('users')
    ->middleware(['auth', 'admin']);

Volt::route('/contasreceber', 'contasreceber.index')
    ->name('contasreceber')
    ->middleware(['auth', 'admin']);

Volt::route('/vendedores', 'vendedores.index')
    ->name('vendedores')
    ->middleware(['auth', 'admin']);

Volt::route('/clientes', 'clientes.index')
    ->name('clientes')
    ->middleware(['auth', 'admin']);