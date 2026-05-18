<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\BroadcastController;

// =======================================================================
// ZONA SHOWCASE UI (Landing Page)
// =======================================================================
Route::get('/', function () {
    return view('shop.index');
});

Route::get('/pos', function () {
    return view('admin.pos');
});

// =======================================================================
// ZONA KATALOG PUBLIK (BISA DILIHAT TANPA LOGIN)
// =======================================================================
Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
Route::get('/customer/categories', [CustomerController::class, 'allCategories'])->name('customer.categories'); // <-- TAMBAHKAN INI
Route::get('/product/{id}', [CustomerController::class, 'productDetail'])->name('product.detail');



// =======================================================================
// ZONA AUTH & ROLE 
// =======================================================================
Route::middleware('auth')->group(function () {
    // ... (rute profile bawaan breeze) ...

    Route::get('/dashboard', function () {
        if (Auth::user()->role === 'admin') {
            return redirect('/admin/dashboard');
        } elseif (Auth::user()->role === 'b2c') {
            return redirect('/customer/dashboard');
        }
        return redirect('/'); 
    })->name('dashboard');
});

// =======================================================================
// ZONA ADMIN / KASIR PARTLYFE
// =======================================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return '<h1>Selamat Datang di Dashboard Admin & POS Partlyfe!</h1>';
    });
});

// =======================================================================
// ZONA PEMBELI ECERAN / B2C (FITUR PRIVAT: KERANJANG, TRANSAKSI, DLL)
// =======================================================================
Route::middleware(['auth', 'role:b2c'])->group(function () {
    // Tampilan Halaman
    Route::get('/customer/cart', [CustomerController::class, 'cart'])->name('customer.cart');
    Route::get('/customer/wishlist', [CustomerController::class, 'wishlist'])->name('customer.wishlist');
    
    // Aksi Keranjang & Wishlist
    Route::post('/cart/add/{id}', [CustomerController::class, 'addToCart'])->name('cart.add');
    Route::patch('/cart/update/{id}', [CustomerController::class, 'updateCart'])->name('cart.update'); // Update Qty
    Route::delete('/cart/remove/{id}', [CustomerController::class, 'removeFromCart'])->name('cart.remove'); // Hapus Item
    Route::post('/wishlist/toggle/{id}', [CustomerController::class, 'toggleWishlist'])->name('wishlist.toggle');
    
    // Rute Lainnya
    Route::get('/customer/transactions', [CustomerController::class, 'transactions'])->name('customer.transactions');
    Route::get('/customer/broadcast', [CustomerController::class, 'broadcast'])->name('customer.broadcast');
    Route::get('/customer/ai-chat', [CustomerController::class, 'aiChat'])->name('customer.ai-chat');
    Route::get('/customer/profile', [CustomerController::class, 'profile'])->name('customer.profile');

    Route::post('/customer/broadcast/mark-read', [CustomerController::class, 'markAllBroadcastsRead'])->name('customer.broadcast.mark-read');

    Route::post('/customer/broadcast/mark-all', [CustomerController::class, 'markAllBroadcastsRead'])->name('customer.broadcast.mark-all-read');
    Route::post('/customer/broadcast/{id}/read', [CustomerController::class, 'markSingleBroadcastRead'])->name('customer.broadcast.read');
    Route::get('/customer/profile', [CustomerController::class, 'profile'])->name('customer.profile');
    Route::put('/customer/profile/update', [CustomerController::class, 'updateProfile'])->name('customer.profile.update');

    Route::get('/customer/ai-chat', [CustomerController::class, 'aiChat'])->name('customer.ai-chat');
    // TAMBAHKAN INI UNTUK MENANGKAP PESAN DARI JAVASCRIPT:
    Route::post('/customer/ai-chat/send', [CustomerController::class, 'sendAiMessage'])->name('customer.ai-chat.send');
    // Route::get('/customer/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
    Route::post('/customer/payment/initiate', [CustomerController::class, 'initiatePayment'])->name('customer.payment.initiate');
    Route::post('/customer/payment/update-status', [CustomerController::class, 'updatePaymentStatus'])->name('customer.payment.update-status');
    Route::get('/customer/invoice/{invoice_number}', [CustomerController::class, 'invoice'])->name('customer.invoice');
    Route::get('/product/{id}', [CustomerController::class, 'show'])->name('product.detail');
});


// KELOMPOK ROUTE KHUSUS ADMIN
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    
    // Dashboard & Customers
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/customers', [DashboardController::class, 'customers'])->name('admin.customers.index'); // <-- Ditambah .index
    Route::get('/customers/{id}', [DashboardController::class, 'showCustomer'])->name('admin.customers.show');
    Route::post('/customers/{id}/upgrade', [DashboardController::class, 'upgradeToB2b'])->name('admin.customers.upgrade');

    // Katalog Produk Admin
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index'); // <-- Ditambah .index

    // Transaksi / Pesanan
    Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index'); // <-- Ditambah .index

    // Broadcast Promosi
    Route::get('/broadcast', [BroadcastController::class, 'index'])->name('admin.broadcast.index'); 
    Route::post('/broadcast', [BroadcastController::class, 'store'])->name('admin.broadcast.store');
});

require __DIR__.'/auth.php';