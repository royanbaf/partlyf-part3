<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Total Pendapatan dari semua transaksi yang sukses
        $totalPendapatan = DB::table('transactions')
            ->whereIn(DB::raw('LOWER(status)'), ['success', 'settlement', 'paid', 'processing', 'sedang diproses', 'selesai'])
            ->sum('total_amount');

        // 2. Hitung Pesanan Baru yang statusnya masih pending
        $pesananBaruCount = DB::table('transactions')
            ->whereIn(DB::raw('LOWER(status)'), ['pending', 'unpaid', 'menunggu', 'menunggu pembayaran'])
            ->count();

        // 3. Hitung Total Macam Produk (SKU) & Berapa banyak yang stoknya nol (0)
        $totalProdukCount = DB::table('products')->count();
        $stokHabisCount = DB::table('products')->where('current_stock', '<=', 0)->count();

        // 4. Hitung Jumlah Total Pelanggan Aktif (B2C)
        $totalPelangganCount = DB::table('users')->where('role', 'b2c')->count();

        // 5. Tarik 5 Transaksi Terbaru beserta Nama Pelanggannya
        $transactions = DB::table('transactions')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->select(
                'transactions.id as transaction_real_id',
                'transactions.invoice_number',
                'transactions.status',
                'transactions.total_amount',
                'transactions.created_at',
                'users.name as customer_name'
            )
            ->latest('transactions.created_at')
            ->take(5)
            ->get();

        // 6. Tarik Daftar Suku Cadang yang stoknya menipis (sisa <= 5 pcs)
        $stokMenipis = DB::table('products')
            ->where('current_stock', '>', 0)
            ->where('current_stock', '<=', 5)
            ->orderBy('current_stock', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPendapatan', 
            'pesananBaruCount', 
            'totalProdukCount', 
            'stokHabisCount', 
            'totalPelangganCount', 
            'transactions', 
            'stokMenipis'
        ));
    }

    // 🚀 BONUS FIX: Tambahkan fungsi customers() ini jika halaman admin/customers memanggil fungsi ini di web.php
    public function customers()
    {
        $customers = DB::table('users')->where('role', 'b2c')->latest()->get();
        return view('admin.customers', compact('customers'));
    }
}