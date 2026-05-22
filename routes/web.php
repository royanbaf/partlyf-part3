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
    // Ambil 5 kategori teratas untuk etalase
    $categories = \App\Models\Category::take(5)->get();
    
    // Ambil 8 suku cadang terbaru/terlaris beserta harga dan fotonya
    $products = \App\Models\Product::with(['prices', 'images'])->latest()->take(8)->get();

    return view('shop.index', compact('categories', 'products'));
});

Route::get('/pos', function () {
    return view('admin.pos');
});

// =======================================================================
// ZONA KATALOG PUBLIK (BISA DILIHAT TANPA LOGIN)
// =======================================================================
Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
Route::get('/customer/categories', [CustomerController::class, 'allCategories'])->name('customer.categories');

// Detail produk diarahkan ke fungsi 'show' karena di sanalah otak AI kita berada
Route::get('/product/{id}', [CustomerController::class, 'show'])->name('product.detail');


// =======================================================================
// ZONA AUTH & ROLE 
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
// ZONA ADMIN / KASIR PARTLYFE
// =======================================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard-pos', function () {
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


// =======================================================================
// KELOMPOK ROUTE KHUSUS ADMIN
// =======================================================================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    
    // Dashboard & Customers
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/customers', [DashboardController::class, 'customers'])->name('admin.customers.index');
    Route::get('/customers/{id}', [DashboardController::class, 'showCustomer'])->name('admin.customers.show');
    Route::post('/customers/{id}/upgrade', [DashboardController::class, 'upgradeToB2b'])->name('admin.customers.upgrade');

    // Transaksi / Pesanan
    Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');

    // Broadcast Promosi
    Route::get('/broadcast', [BroadcastController::class, 'index'])->name('admin.broadcast.index'); 
    Route::post('/broadcast', [BroadcastController::class, 'store'])->name('admin.broadcast.store');

    // 🚀 ROUTE BINDING CRUD PRODUK ADMIN BARU
    // Diberi penamaan otomatis agar menghasilkan rute 'admin.products.index' dengan benar
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class)->names('admin.products');
});

// Rute untuk Live Search berbasis AI
Route::get('/api/search-ai', [App\Http\Controllers\CustomerController::class, 'aiSearch'])->name('api.search.ai');

require __DIR__.'/auth.php';