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
        $customers = DB::table('users')->whereIn('role', ['b2c', 'B2C', 'b2b', 'B2B'])->latest()->get();
        return view('admin.customers', compact('customers'));
    }

    public function toggleB2bRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = DB::table('users')->where('id', $request->user_id)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan.'
            ], 404);
        }

        $currentRole = strtolower($user->role);
        $newRole = $currentRole === 'b2b' ? 'B2C' : 'B2B';
        
        DB::table('users')->where('id', $request->user_id)->update([
            'role' => $newRole
        ]);

        return response()->json([
            'success' => true,
            'message' => $newRole === 'B2B' ? 'Tingkatan pelanggan dinaikkan menjadi B2B' : 'Tingkatan pelanggan dikembalikan ke B2C',
            'new_role' => $newRole,
            'button_text' => $newRole === 'B2B' ? 'TURUNKAN KE B2C' : 'JADI B2B',
            'badge_text' => $newRole === 'B2B' ? 'Mitra B2B' : 'Retail B2C',
            'badge_class' => $newRole === 'B2B' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20'
        ]);
    }

    // 🚀 FIX MUTLAK SAKTI: Mengembalikan UI dan Menghubungkan Data Transaksi Asli
    // 🚀 FIX MUTLAK: Mengunci Query ke transactions.customer_id sesuai DB Kelompok
    // 🚀 FIX MUTLAK FINAL: Menembak langsung kolom user_id sesuai dump radar database kelompok
    public function showCustomer($id)
    {
        // 1. Ambil data profil pelanggan berdasarkan ID yang diklik
        $customer = DB::table('users')
            ->where('id', $id)
            ->first();

        if (!$customer) {
            abort(404, 'Pelanggan tidak ditemukan.');
        }

        // 2. Tarik riwayat transaksi murni dari tabel transactions kelompokmu menggunakan user_id
        $customerTransactions = DB::table('transactions')
            ->where('user_id', $id) // ✨ Sesuai dump array index ke-2 kelompokmu
            ->latest('created_at')
            ->get();

        // 3. Lempar datanya dengan aman ke file blade khusus detail pelanggan
        return view('admin.customer_detail', compact('customer', 'customerTransactions'));
    }
}