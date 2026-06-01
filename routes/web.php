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
// ZONA SHOWCASE UI (Landing Page Publik)
// =======================================================================
Route::get('/', function () {
    // Ambil 5 kategori teratas untuk etalase
    $categories = \App\Models\Category::take(5)->get();
    
    // Ambil 8 suku cadang terbaru/terlaris beserta harga dan fotonya
    $products = \App\Models\Product::with(['prices', 'images'])->latest()->take(8)->get();

    return view('shop.index', compact('categories', 'products'));
});


// =======================================================================
// ZONA KATALOG PUBLIK (BISA DILIHAT TANPA LOGIN)
// =======================================================================
Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
Route::get('/customer/categories', [CustomerController::class, 'allCategories'])->name('customer.categories');

// Detail produk diarahkan ke fungsi 'show' karena di sanalah otak AI kita berada
Route::get('/product/{id}', [CustomerController::class, 'show'])->name('product.detail');


// =======================================================================
// ZONA AUTH & ROLE LAYER UTAMA
// =======================================================================
Route::middleware('auth')->group(function () {
    
    // Rute bawaan Breeze untuk pengaturan dasar (Jangan dihapus)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Pengatur Arah Login (Redirector)
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
// ZONA ADMIN / KASIR PARTLYFE (PENGAMAN FILTER DI BAWAH PREFIX /ADMIN)
// =======================================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    
    // 🏪 1. SINKRONISASI POS: Halaman fisik POS Kasir diamankan di sini agar klop dengan link /admin/pos
    Route::get('/pos', function() {
        return view('admin.pos');
    })->name('admin.pos.page');

    // 💰 2. Rute POST AJAX Transaksi ditaruh di paling atas agar tidak tertimpa wildcard
    Route::post('/transactions/update-status', [TransactionController::class, 'updateStatus'])->name('admin.transactions.updateStatus');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');
    Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('admin.transactions.show');

    // 📊 3. Dashboard Utama Admin & Data Pelanggan
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/customers', [DashboardController::class, 'customers'])->name('admin.customers.index');
    Route::get('/customers/{id}', [DashboardController::class, 'showCustomer'])->name('admin.customers.show');
    Route::post('/customers/{id}/upgrade', [DashboardController::class, 'upgradeToB2b'])->name('admin.customers.upgrade');
    
    // 📣 4. Kirim Broadcast Promo (Hanya Satu Pasang Rute Resmi)
    Route::get('/broadcast', [BroadcastController::class, 'index'])->name('admin.broadcast.index');
    Route::post('/broadcast', [BroadcastController::class, 'store'])->name('admin.broadcast.store');

    // 📦 5. Resource CRUD Produk Admin Otomatis
    Route::resource('products', ProductController::class)->names('admin.products');
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
    Route::patch('/cart/update/{id}', [CustomerController::class, 'updateCart'])->name('cart.update'); 
    Route::delete('/cart/remove/{id}', [CustomerController::class, 'removeFromCart'])->name('cart.remove'); 
    Route::post('/wishlist/toggle/{id}', [CustomerController::class, 'toggleWishlist'])->name('wishlist.toggle');
    
    // Rute Transaksi & Kabar Admin
    Route::get('/customer/transactions', [CustomerController::class, 'transactions'])->name('customer.transactions');
    Route::get('/customer/broadcast', [CustomerController::class, 'broadcast'])->name('customer.broadcast');
    Route::post('/customer/broadcast/mark-read', [CustomerController::class, 'markAllBroadcastsRead'])->name('customer.broadcast.mark-read');
    Route::post('/customer/broadcast/mark-all', [CustomerController::class, 'markAllBroadcastsRead'])->name('customer.broadcast.mark-all-read');
    Route::post('/customer/broadcast/{id}/read', [CustomerController::class, 'markSingleBroadcastRead'])->name('customer.broadcast.read');

    // Rute Tanya Mekanik AI
    Route::get('/customer/ai-chat', [CustomerController::class, 'aiChat'])->name('customer.ai-chat');
    Route::post('/customer/ai-chat/send', [CustomerController::class, 'sendAiMessage'])->name('customer.ai-chat.send');
    
    // 🚀 Rute Pembayaran Midtrans & Ringkasan Belanja (Checkout)
    Route::get('/customer/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
    Route::post('/customer/payment/initiate', [CustomerController::class, 'initiatePayment'])->name('customer.payment.initiate');
    Route::post('/customer/payment/update-status', [CustomerController::class, 'updatePaymentStatus'])->name('customer.payment.update-status');
    Route::get('/customer/invoice/{invoice_number}', [CustomerController::class, 'invoice'])->name('customer.invoice');

    // RUTE PROFIL BARU (TERINTEGRASI PENUH DENGAN DATABASE)
    Route::get('/customer/profile', [ProfileController::class, 'index'])->name('customer.profile');
    Route::put('/customer/profile/bio', [ProfileController::class, 'updateBio'])->name('profile.update.bio');
    Route::put('/customer/profile/address', [ProfileController::class, 'updateAddress'])->name('profile.update.address');
    Route::post('/customer/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update.avatar');
    Route::put('/customer/profile/security', [ProfileController::class, 'updateSecurity'])->name('profile.update.security');
});

// Rute untuk Live Search berbasis AI Publik
Route::get('/api/search-ai', [CustomerController::class, 'aiSearch'])->name('api.search.ai');

// 📦 Resource CRUD Produk Admin Otomatis
    Route::resource('products', ProductController::class)->names('admin.products');

    // 🏪 TAMBAHAN RUTE BARU KHUSUS INTEGRASI MIDtrans & POTONG STOK DI KASIR POS
    Route::post('/pos/initiate', [TransactionController::class, 'initiatePosPayment'])->name('admin.pos.initiate');
    Route::post('/pos/complete', [TransactionController::class, 'completePosPayment'])->name('admin.pos.complete');

// Sertakan rute otentikasi bawaan Breeze
require __DIR__.'/auth.php';