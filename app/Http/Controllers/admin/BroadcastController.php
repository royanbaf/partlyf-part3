<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
// use App\Models\Broadcast; // Uncomment jika nanti model Broadcast sudah ada

class BroadcastController extends Controller
{
    public function index()
    {
        // SUNTIKAN: Ambil jumlah pelanggan untuk diinfokan di halaman broadcast ("Kirim pesan ke X pelanggan")
        $customerCount = User::whereIn('role', ['b2c', 'b2b'])->count();

        return view('admin.broadcast', compact('customerCount'));
    }

    public function store(Request $request)
    {
        // Validasi input form broadcast
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // LOGIKA PENYIMPANAN (Nanti disesuaikan dengan tabel broadcasts)
        /*
        Broadcast::create([
            'title' => $request->title,
            'message' => $request->message,
            'status' => 'sent'
        ]);
        */

        return back()->with('success', 'Pesan Broadcast Promosi berhasil dikirim ke semua pelanggan!');
    }
}