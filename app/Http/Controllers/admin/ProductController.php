<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // SUNTIKAN INTEGRASI: Ambil data produk berserta relasi Kategori, Gambar, dan Harga
        // Menggunakan paginate(10) agar kalau datanya ratusan tidak ngelag
        $products = Product::with(['category', 'images', 'prices'])
                    ->latest()
                    ->paginate(10);

        // Ambil kategori untuk filter atau modal tambah produk nantinya
        $categories = Category::all();

        // Mengirim variabel $products dan $categories ke view admin
        return view('admin.products', compact('products', 'categories'));
    }

    // Nanti bisa ditambahkan function create(), store(), edit(), update(), destroy() di sini
}