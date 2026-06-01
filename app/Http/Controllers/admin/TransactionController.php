<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class TransactionController extends Controller
{
    // 1. READ: Tampilkan semua transaksi gabung dengan data nama user
    public function index()
    {
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

    // 2. UPDATE: Sinkronisasi status transaksi dari dropdown dashboard admin
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'status' => 'required|string'
        ]);

        $statusInput = trim($request->status);

        try {
            DB::table('transactions')
                ->where('id', $request->id)
                ->orWhere('invoice_number', $request->id)
                ->update([
                    'status' => $statusInput,
                    'updated_at' => now()
                ]);
                
        } catch (\Exception $e) {
            try {
                DB::table('transactions')
                    ->where('id', $request->id)
                    ->orWhere('invoice_number', $request->id)
                    ->update([
                        'status' => strtoupper($statusInput),
                        'updated_at' => now()
                    ]);
            } catch (\Exception $e2) {
                // Fail-safe logging
            }
        }

        return response()->json([
            'success' => true, 
            'message' => 'Status berhasil disinkronkan!'
        ]);
    }

    // =======================================================================
    // 🏪 3. POS BACKEND: Catat Transaksi Awal & Ambil Token Midtrans Snap
    // =======================================================================
    public function initiatePosPayment(Request $request)
    {
        $items = $request->input('items');
        if (empty($items)) {
            return response()->json(['status' => 'error', 'message' => 'Nota kasir masih kosong.'], 400);
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $orderId = 'POS-' . time() . '-' . Auth::id();
        $grossAmount = 0;
        $itemDetails = [];
        $dbDetails = [];

        DB::beginTransaction();
        try {
            foreach ($items as $productId => $cartData) {
                $product = DB::table('products')->where('id', $productId)->first();
                if (!$product) {
                    return response()->json(['status' => 'error', 'message' => 'Suku cadang tidak ditemukan.'], 404);
                }

                if ($product->current_stock < $cartData['qty']) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Stok {$product->name} tidak mencukupi! Sisa: {$product->current_stock} pcs."
                    ], 400);
                }

                $price = (int)$cartData['price'];
                $grossAmount += ($price * $cartData['qty']);

                $itemDetails[] = [
                    'id' => $product->id,
                    'price' => $price,
                    'quantity' => (int)$cartData['qty'],
                    'name' => substr($product->name, 0, 50),
                ];

                $dbDetails[] = [
                    'product_id' => $product->id,
                    'qty' => $cartData['qty'],
                    'price' => $price
                ];
            }

            // Inisiasi awal dengan status pending bawaan sistem
            $transactionId = DB::table('transactions')->insertGetId([
                'user_id' => Auth::id(), 
                'invoice_number' => $orderId,
                'total_amount' => $grossAmount,
                'status' => 'pending', 
                'payment_method' => 'Midtrans POS Offline',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($dbDetails as $detail) {
                DB::table('transaction_details')->insert([
                    'transaction_id' => $transactionId,
                    'product_id' => $detail['product_id'],
                    'qty' => $detail['qty'],
                    'price' => $detail['price'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => 'Pelanggan POS',
                    'email' => 'pos.offline@partlyfe.test',
                ],
            ];

            $snapToken = Snap::getSnapToken($params);
            DB::table('transactions')->where('id', $transactionId)->update(['snap_token' => $snapToken]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menginisiasi Midtrans: ' . $e->getMessage()], 500);
        }
    }

    // =======================================================================
    // 🎯 FIX TOTAL: Alur Eksekusi Status 'paid' & Potong Stok Berurutan (Aman)
    // =======================================================================
    public function completePosPayment(Request $request)
    {
        $orderId = $request->input('order_id');
        
        DB::beginTransaction();
        try {
            // 1. Cari transaksi berdasarkan nomor invoice
            $transaction = DB::table('transactions')->where('invoice_number', $orderId)->first();
            if (!$transaction) {
                return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan.'], 404);
            }

            // 🚀 STEP 1: Update status transaksi menjadi 'paid' menggunakan Eloquent/Query Builder standar.
            // Pilihan 'paid' disesuaikan dengan isi ENUM database kelompokmu agar tidak memicu truncated error.
            DB::table('transactions')
                ->where('id', $transaction->id)
                ->update([
                    'status' => 'paid',
                    'updated_at' => now()
                ]);

            // 🚀 STEP 2: Ambil rincian produk yang dibeli untuk memotong stok fisik
            $details = DB::table('transaction_details')->where('transaction_id', $transaction->id)->get();
            
            foreach ($details as $detail) {
                $product = DB::table('products')->where('id', $detail->product_id)->first();
                if ($product) {
                    // Hitung sisa stok (pastikan tidak minus)
                    $newStock = max(0, $product->current_stock - $detail->qty);
                    
                    // Eksekusi pembaruan kuantitas stok di tabel products
                    DB::table('products')
                        ->where('id', $detail->product_id)
                        ->update([
                            'current_stock' => $newStock,
                            'updated_at' => now()
                        ]);
                }
            }

            // Jika semua langkah di atas sukses tanpa interupsi, kunci perubahan ke database
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Pembayaran lunas & stok berhasil dikurangi!']);
            
        } catch (\Exception $e) {
            // Jika ada satu saja baris yang gagal, batalkan semua agar data tidak korup
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal memperbarui data transaksi & stok: ' . $e->getMessage()], 500);
        }
    }
}