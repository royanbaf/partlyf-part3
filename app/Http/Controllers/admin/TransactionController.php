<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // SUNTIKAN INTEGRASI: Ambil data transaksi berserta nama User/Pelanggan yang beli
        $transactions = Transaction::with('user')->latest()->paginate(15);

        return view('admin.transactions', compact('transactions'));
    }
    
    // Nanti bisa ditambahkan function show($id) untuk melihat detail nota / resi struk
}