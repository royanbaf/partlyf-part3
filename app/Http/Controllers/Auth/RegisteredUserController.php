<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Tampilkan halaman formulir registrasi.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Proses data kiriman dari form registrasi web.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi input agar wajib diisi, email harus valid & unik, password harus cocok
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Eksekusi simpan ke database dengan mengunci role langsung ke 'b2c'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password demi keamanan b2c
            'role' => 'b2c', // <-- PENGUNCIAN ROLE OTOMATIS JADI CUSTOMER
        ]);

        // 3. Picu event bawaan Laravel Breeze
        event(new Registered($user));

        // 4. Otomatis buat user langsung dalam kondisi Logged In (Masuk Akun)
        Auth::login($user);

        // 5. Alihkan secara instan ke halaman utama katalog milik Customer B2C
        return redirect(route('customer.dashboard', absolute: false));
    }
}