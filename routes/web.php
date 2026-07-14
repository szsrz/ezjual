<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::livewire('p/{product:slug}', 'pages::products.show')->name('products.show');

require __DIR__.'/settings.php';
require __DIR__.'/sales.php';
