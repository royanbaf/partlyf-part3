<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // Langsung memanggil admin/transactions.blade.php
        return view('admin.transactions');
    }
}