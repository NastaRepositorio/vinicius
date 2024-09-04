<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/home-seller', 'home_seller.index')
    ->name("home_seller") 
    ->middleware(['auth','seller']);

Volt::route('/meus-lancamentos', 'seller_lancamentos.index')
    ->name("seller_lancamentos")
    ->middleware(['auth','seller']);