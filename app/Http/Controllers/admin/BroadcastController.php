<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BroadcastController extends Controller
{
    // Tampilkan halaman form broadcast
    public function index()
    {
        // Ambil riwayat broadcast terakhir untuk dipajang di halaman admin
        $broadcasts = DB::table('broadcasts')->latest()->get();
        return view('admin.broadcast', compact('broadcasts'));
    }

    // Eksekusi kirim pesan ke semua customer
   public function store(Request $request)
{
    // 1. Validasi input form
    $request->validate([
        'title' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    // 2. Masukkan pesan ke tabel dengan menyertakan user_id DAN type
    DB::table('broadcasts')->insert([
        'user_id'    => Auth::id(),
        'type'       => 'promo', // <-- Tambah kolom penyelemat ini, Wak! (bisa diisi 'promo' atau 'info' sesuai struktur timmu)
        'title'      => $request->title,
        'message'    => $request->message,
        'is_read'    => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 3. Kembalikan ke halaman dengan pesan sukses
    return redirect()->route('admin.broadcast.index')->with('success', 'Pengumuman promo premium berhasil disiarkan ke seluruh customer!');
    }
}