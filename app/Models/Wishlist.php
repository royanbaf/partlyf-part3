<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    // Mengizinkan semua kolom diisi secara massal kecuali ID
    protected $guarded = ['id'];

    // Relasi: Wishlist ini milik siapa (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Wishlist ini menyimpan barang apa (Product)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}