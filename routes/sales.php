<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('produk', 'pages::admin.products.index')->name('products.index');
    Route::livewire('produk/baru', 'pages::admin.products.create')->name('products.create');
    Route::livewire('produk/{product}/edit', 'pages::admin.products.edit')->name('products.edit');

    Route::livewire('pesanan', 'pages::admin.orders.index')->name('orders.index');
    Route::livewire('pesanan/baru', 'pages::admin.orders.create')->name('orders.create');

    Route::livewire('voucher', 'pages::admin.vouchers.index')->name('vouchers.index');
    Route::livewire('voucher/baru', 'pages::admin.vouchers.create')->name('vouchers.create');
    Route::livewire('voucher/{voucher}/edit', 'pages::admin.vouchers.edit')->name('vouchers.edit');
});
