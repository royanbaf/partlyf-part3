<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Cari user pertama sebagai pembeli dummy
        $user = User::first(); 
        if (!$user) return; // Skip kalau belum ada user

        // Ambil 3 produk acak untuk dijadikan bahan transaksi
        $products = Product::with('prices')->inRandomOrder()->take(3)->get();
        if ($products->count() < 3) return;

        // -- Transaksi 1: Selesai (Delivered)
        $t1 = Transaction::create([
            'invoice_number' => 'INV-PL-' . time() . '1',
            'user_id' => $user->id,
            'total_amount' => ($products[0]->prices->first()->price ?? 0) * 2,
            'status' => 'delivered',
            'payment_method' => 'Bank Transfer (BCA)',
            'shipping_address' => 'Jl. Boulevard CitraRaya Blok A No. 1, Surabaya',
        ]);
        TransactionDetail::create([
            'transaction_id' => $t1->id,
            'product_id' => $products[0]->id,
            'qty' => 2,
            'price' => $products[0]->prices->first()->price ?? 0
        ]);

        // -- Transaksi 2: Sedang Diproses (Processing)
        $t2 = Transaction::create([
            'invoice_number' => 'INV-PL-' . time() . '2',
            'user_id' => $user->id,
            'total_amount' => ($products[1]->prices->first()->price ?? 0) * 1 + ($products[2]->prices->first()->price ?? 0) * 1,
            'status' => 'processing',
            'payment_method' => 'E-Wallet (GoPay)',
            'shipping_address' => 'Gedung Universitas Ciputra, Ruang Lab AI',
        ]);
        TransactionDetail::create([
            'transaction_id' => $t2->id,
            'product_id' => $products[1]->id,
            'qty' => 1,
            'price' => $products[1]->prices->first()->price ?? 0
        ]);
        TransactionDetail::create([
            'transaction_id' => $t2->id,
            'product_id' => $products[2]->id,
            'qty' => 1,
            'price' => $products[2]->prices->first()->price ?? 0
        ]);

    }
}   