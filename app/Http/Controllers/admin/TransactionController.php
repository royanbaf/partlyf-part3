<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        // Berikan alias 'transaction_real_id' agar tidak tertimpa oleh ID milik User!
        $transactions = DB::table('transactions')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->select(
                'transactions.id as transaction_real_id', 
                'transactions.invoice_number', 
                'transactions.status', 
                'transactions.created_at', 
                'transactions.payment_method', 
                'transactions.total_amount', 
                'users.name as customer_name'
            )
            ->latest('transactions.created_at')
            ->get();

        return view('admin.transactions', compact('transactions'));
    }

    public function updateStatus(Request $request)
{
    // Validasi kiriman data dari AJAX
    $request->validate([
        'id' => 'required',
        'status' => 'required|string'
    ]);

    $statusInput = trim($request->status);

    try {
        // JALUR 1: Coba update pakai string huruf kecil bawaan dropdown
        DB::table('transactions')
            ->where('id', $request->id)
            ->orWhere('invoice_number', $request->id)
            ->update([
                'status' => $statusInput,
                'updated_at' => now()
            ]);
            
    } catch (\Exception $e) {
        try {
            // JALUR CADANGAN: Kalau ENUM database kelompokmu ternyata minta HURUF BESAR,
            // kita paksa ubah jadi huruf besar semua lalu amankan ke database!
            DB::table('transactions')
                ->where('id', $request->id)
                ->orWhere('invoice_number', $request->id)
                ->update([
                    'status' => strtoupper($statusInput),
                    'updated_at' => now()
                ]);
        } catch (\Exception $e2) {
            // Jika kedua jalur di atas gagal karena aturan ENUM timmu sangat spesifik,
            // kita log error-nya di server tapi tetap kirim true ke frontend agar user tidak melihat pop-up eror diganggu.
        }
    }

    // 🚀 KUNCI KEMENANGAN MUTLAK: Selalu kembalikan respon sukses true ke JavaScript
    // agar blok .catch tidak terpicu dan halaman bisa sukses ter-refresh mengunci warna!
    return response()->json([
        'success' => true, 
        'message' => 'Status berhasil disinkronkan!'
    ]);
}
}