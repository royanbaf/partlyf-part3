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
        $categories = DB::table('categories')->get();
        return view('admin.products', compact('products', 'categories'));
    }

    // =======================================================================
    // 🎯 FIX FUNCTION: Alihkan Tampilan ke Halaman Utama dengan Pemicu Modal
    // =======================================================================
    public function edit($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        
        if (!$product) {
            abort(404, 'Suku cadang tidak ditemukan di database.');
        }

        $priceRow = DB::table('product_prices')
            ->where('product_id', $id)
            ->where('price_level', 1)
            ->first();

        $product->price = $priceRow ? $priceRow->price : 0;

        $imageRow = DB::table('product_images')->where('product_id', $id)->first();
        $product->image = $imageRow ? $imageRow->image_path : null;

        $products = DB::table('products')->latest('id')->get();
        $categories = DB::table('categories')->get();

        return view('admin.products', compact('products', 'product', 'categories'))->with('open_edit_modal', $id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'current_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $itemCode = $request->item_code;
        if (empty($itemCode)) {
            $category = DB::table('categories')->where('id', $request->category_id)->first();
            $categoryPrefix = strtoupper(substr($category->name ?? 'GEN', 0, 3));
            $namePrefix = strtoupper(substr($request->name ?? 'PRD', 0, 3));
            $timestamp = substr(now()->format('Y'), -2);
            $itemCode = $categoryPrefix . '-' . $namePrefix . '-' . $timestamp;
        }

        DB::beginTransaction();
        try {
            $productId = DB::table('products')->insertGetId([
                'name' => $request->name,
                'brand' => $request->brand,
                'item_code' => $itemCode,
                'category_id' => $request->category_id,
                'current_stock' => $request->current_stock,
                'min_stock' => $request->min_stock,
                'description' => $request->description,
                'rack_location' => $request->rack_location,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('product_prices')->insert([
                'product_id' => $productId,
                'price_level' => 1,
                'price' => $request->price,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/products', $fileName);

                DB::table('product_images')->insert([
                    'product_id' => $productId,
                    'image_path' => 'products/' . $fileName,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Produk berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambah produk: ' . $e->getMessage());
        }
    }

public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'current_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'name' => $request->name,
                'brand' => $request->brand,
                'category_id' => $request->category_id,
                'current_stock' => $request->current_stock,
                'min_stock' => $request->min_stock,
                'updated_at' => now()
            ];

            if ($request->has('item_code') && !empty($request->item_code)) {
                $updateData['item_code'] = $request->item_code;
            }

            $updateData['description'] = $request->description;
            $updateData['rack_location'] = $request->rack_location;

            DB::table('products')
                ->where('id', $id)
                ->update($updateData);

            DB::table('product_prices')
                ->where('product_id', $id)
                ->where('price_level', 1)
                ->update([
                    'price' => $request->price,
                    'updated_at' => now()
                ]);

            if ($request->hasFile('image')) {
                $oldImage = DB::table('product_images')->where('product_id', $id)->first();
                if ($oldImage) {
                    Storage::delete('public/' . $oldImage->image_path);
                    DB::table('product_images')->where('id', $oldImage->id)->delete();
                }

                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/products', $fileName);

                DB::table('product_images')->insert([
                    'product_id' => $id,
                    'image_path' => 'products/' . $fileName,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
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