<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasFactory;

    // 🚀 SINKRONISASI KUNCI: Pastikan nama tabelnya huruf kecil semua sesuai phpMyAdmin
    protected $table = 'broadcasts';

    protected $fillable = [
        'user_id', // 🚀 Tambahkan ini
        'title',
        'message',
        'type',    // 🚀 Tambahkan ini
        'is_read',
    ];
}