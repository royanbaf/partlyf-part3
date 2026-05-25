<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BroadcastController extends Controller
{
    public function index()
    {
        // Langsung memanggil admin/broadcast.blade.php
        return view('admin.broadcast');
    }

    public function store(Request $request)
    {
        // Logika simpan broadcast nanti di sini
        return back()->with('success', 'Pesan berhasil dikirim!');
    }
}