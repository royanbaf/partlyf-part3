<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Broadcast; // 🚀 SEKARANG SUDAH AKTIF

class BroadcastController extends Controller
{
    public function index()
    {
        // Ambil jumlah pelanggan untuk diinfokan di halaman broadcast ("Kirim pesan ke X pelanggan")
        $customerCount = User::whereIn('role', ['b2c', 'b2b'])->count();

        return view('admin.broadcast', compact('customerCount'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input form
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            // 2. Simpan data lengkap dengan user_id admin dan type default
            \App\Models\Broadcast::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(), // 🚀 SUNTIKAN KUNCI: ID Admin yang kirim promo
                'title'   => $request->title,
                'message' => $request->message,
                'type'    => 'promo', // 🚀 SUNTIKAN KUNCI: Isi default type (misal: 'promo' atau 'global')
                'is_read' => false,
            ]);

            return back()->with('success', 'Pesan Broadcast Promosi berhasil dikirim ke semua pelanggan!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyebarkan informasi: ' . $e->getMessage());
        }
    }
}