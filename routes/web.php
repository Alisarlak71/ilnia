<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Livewire\CartPage;
use App\Livewire\LoginPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::livewire('/cart', CartPage::class)->name('cart');

// Customer login with mobile + one-time code (limits are enforced in OtpService).
Route::livewire('/login', LoginPage::class)->middleware('guest')->name('login');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');
