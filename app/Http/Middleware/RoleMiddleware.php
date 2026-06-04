<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // 1. Cek apakah user belum login
        if (!Auth::check()) {
            return redirect('login');
        }

        // 2. Pecah role yang diizinkan (biar support multiple role, misal: 'admin|b2b')
        $userRoles = explode('|', $role);
        $currentUserRole = strtolower(Auth::user()->role);

        // 3. Cek apakah role user yang sedang login ada di daftar yang diizinkan
        if (!in_array($currentUserRole, $userRoles)) {
            // Kalau role-nya nggak cocok, tendang ke halaman error 403
            abort(403, 'Akses Ditolak! Anda tidak punya izin untuk membuka halaman ini.');
        }

        // 4. Kalau aman, silakan masuk
        return $next($request);
    }
}