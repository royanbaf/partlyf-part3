<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Menampilkan halaman Dashboard Overview Utama Admin
    public function index()
    {
        // KITA SUNTIKKAN DATA STATISTIK NYATA DARI DATABASE
        $totalProducts = Product::count();
        $totalCustomers = User::whereIn('role', ['b2c', 'b2b'])->count();
        $totalTransactions = Transaction::count(); // Pastikan model Transaction sudah ada
        $lowStockProducts = Product::where('current_stock', '<=', 5)->count(); // Produk mau habis

        return view('admin.dashboard', compact(
            'totalProducts', 
            'totalCustomers', 
            'totalTransactions',
            'lowStockProducts'
        ));
    }

    // 1. Menampilkan Semua Pelanggan (B2C & B2B) dari Database
    public function customers()
    {
        // Mengambil semua user pelanggan, diurutkan dari yang terbaru
        $customers = User::whereIn('role', ['b2c', 'b2b'])->latest()->get();

        return view('admin.customers', compact('customers'));
    }

    // 2. Melihat Detail Akun & Riwayat Pembelian Pelanggan
    public function showCustomer($id)
    {
        $customer = User::findOrFail($id);
        
        // Mengambil seluruh nota transaksi milik pelanggan terkait (relasi ke detail)
        $transactions = Transaction::where('user_id', $id)->latest()->get();

        return view('admin.customers.show', compact('customer', 'transactions'));
    }

    // 3. Menangani Fitur Upgrade Tingkat Akun dari Retail (B2C) ke Mitra Bisnis (B2B)
    public function upgradeToB2b($id)
    {
        $customer = User::findOrFail($id);
        
        if ($customer->role === 'b2c') {
            $customer->role = 'b2b';
            $customer->save();
            
            return redirect()->back()->with('success', "Berhasil! Akun {$customer->name} kini resmi di-upgrade menjadi Mitra B2B.");
        }

        return redirect()->back()->with('error', 'Pengguna ini sudah berstatus sebagai mitra B2B.');
    }
}