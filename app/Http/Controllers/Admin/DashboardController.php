<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function customers()
    {
        // Langsung memanggil admin/customers.blade.php
        return view('admin.customers');
    }
}