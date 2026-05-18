<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Validasi kredensial email & password pembeli/admin
        $request->authenticate();

        // 2. Amankan session untuk mencegah serangan Session Fixation
        $request->session()->regenerate();

        // 3. Ambil data user yang baru saja berhasil masuk
        $user = Auth::user();

        // 4. Lemparkan user langsung ke halaman utama masing-masing sesuai Role
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        } elseif ($user->role === 'b2c') {
            return redirect()->intended(route('customer.dashboard'));
        }

        // Jalur cadangan (fallback) jika rute di atas tidak sengaja terlewati
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}