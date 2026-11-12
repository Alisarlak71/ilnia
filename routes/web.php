<?php

use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Livewire\AddressesPage;
use App\Livewire\CartPage;
use App\Livewire\CheckoutPage;
use App\Livewire\LoginPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::livewire('/cart', CartPage::class)->name('cart');

// Content pages (edited in the panel; 404 until published, except for admins previewing).
Route::get('/about', [PageController::class, 'show'])->defaults('slug', 'about')->name('pages.about');
Route::get('/contact', [PageController::class, 'show'])->defaults('slug', 'contact')->name('pages.contact');
Route::get('/shipping', [PageController::class, 'show'])->defaults('slug', 'shipping')->name('pages.shipping');
Route::get('/terms', [PageController::class, 'show'])->defaults('slug', 'terms')->name('pages.terms');
Route::livewire('/checkout', CheckoutPage::class)->middleware('auth')->name('checkout');

// Return from the payment gateway (no login required; verified on the server).
Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/result/{order:number}', [PaymentController::class, 'result'])->middleware('auth')->name('payment.result');
Route::post('/payment/retry/{order:number}', [PaymentController::class, 'retry'])->middleware(['auth', 'throttle:10,1'])->name('payment.retry');

// Customer login with mobile + one-time code (limits are enforced in OtpService).
Route::livewire('/login', LoginPage::class)->middleware('guest')->name('login');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

// Customer account (logged-in customers only).
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/orders', [AccountOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{number}', [AccountOrderController::class, 'show'])->name('orders.show');
    Route::livewire('/addresses', AddressesPage::class)->name('addresses');
});
