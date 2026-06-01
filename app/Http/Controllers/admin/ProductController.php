<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 1. READ: Tampilkan semua produk dan ambil data gambar pertamanya
    public function index()
    {
        $products = DB::table('products')->latest('id')->get();
        return view('admin.products', compact('products'));
    }

    // =======================================================================
    // 🎯 FIX FUNCTION: Alihkan Tampilan ke Halaman Utama dengan Pemicu Modal
    // =======================================================================
    public function edit($id)
    {
        // 1. Ambil data produk menggunakan Query Builder murni kelompokmu
        $product = DB::table('products')->where('id', $id)->first();
        
        if (!$product) {
            abort(404, 'Suku cadang tidak ditemukan di database.');
        }

        // 2. Tarik harga level 1 (Retail) suku cadang ini
        $priceRow = DB::table('product_prices')
            ->where('product_id', $id)
            ->where('price_level', 1)
            ->first();

        $product->price = $priceRow ? $priceRow->price : 0;

        // 3. Ambil semua data produk untuk merender tabel utamanya
        $products = DB::table('products')->latest('id')->get();

        // 4. JURUS KUNCI: Buka file products.blade.php bawaan kelompokmu
        // dan kirim variabel 'open_edit_modal' berisi data produk target
        return view('admin.products', compact('products', 'product'))->with('open_edit_modal', $id);
    }
    // 2. CREATE: Simpan produk baru + Unggah Foto sesuai kolom database phpMyAdmin
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'item_code' => 'required|string',
            'current_stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        DB::beginTransaction();
        try {
            // Masukkan data dasar ke tabel products
            $productId = DB::table('products')->insertGetId([
                'name' => $request->name,
                'brand' => $request->brand,
                'item_code' => $request->item_code,
                'current_stock' => $request->current_stock,
                'category_id' => $request->category_id ?? 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Masukkan harga ke tabel product_prices (price_level 1)
            DB::table('product_prices')->insert([
                'product_id' => $productId,
                'price_level' => 1,
                'price' => $request->price,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Proses Enkripsi Unggah Foto ke Storage MacBook
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/products', $fileName);

                // Catat ke tabel relasi foto (product_images)
                DB::table('product_images')->insert([
                    'product_id' => $productId,
                    'image_path' => 'products/' . $fileName,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Produk dan Foto berhasil disimpan ke database!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambah produk: ' . $e->getMessage());
        }
    }

    // 3. UPDATE: Perbarui data produk via Modal Button / Form Edit Restock
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'item_code' => 'required|string',
            'current_stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        DB::beginTransaction();
        try {
            // Update data di tabel products
            DB::table('products')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'brand' => $request->brand,
                    'item_code' => $request->item_code,
                    'current_stock' => $request->current_stock,
                    'updated_at' => now()
                ]);

            // Update harga di tabel product_prices
            DB::table('product_prices')
                ->where('product_id', $id)
                ->where('price_level', 1)
                ->update([
                    'price' => $request->price,
                    'updated_at' => now()
                ]);

            // Jika admin mengganti atau menambahkan foto baru saat edit
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/products', $fileName);

                // Hapus pencatatan foto lama jika ada
                $oldImage = DB::table('product_images')->where('product_id', $id)->first();
                if ($oldImage) {
                    Storage::delete('public/' . $oldImage->image_path);
                    DB::table('product_images')->where('id', $oldImage->id)->delete();
                }

                // Masukkan foto baru
                DB::table('product_images')->insert([
                    'product_id' => $id,
                    'image_path' => 'products/' . $fileName,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            
            // 🚀 ALKAN ALUR: Lempar balik ke dashboard utama admin agar perubahan langsung terpantau live
            return redirect()->route('admin.dashboard')->with('success', 'Data stok suku cadang berhasil diperbarui di database!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui data produk.');
        }
    }

    // 4. DESTROY: Hapus permanen suku cadang beserta harga dan berkas fotonya
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Hapus file fisik foto di storage terlebih dahulu
            $oldImage = DB::table('product_images')->where('product_id', $id)->first();
            if ($oldImage) {
                Storage::delete('public/' . $oldImage->image_path);
            }

            // Hapus baris data di tabel anak
            DB::table('product_images')->where('product_id', $id)->delete();
            DB::table('product_prices')->where('product_id', $id)->delete();
            
            // Hapus produk utama
            DB::table('products')->where('id', $id)->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Produk berhasil dihapus permanen dari sistem!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus produk.');
        }
    }
}