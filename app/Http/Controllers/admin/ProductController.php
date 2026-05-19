<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 1. READ: Tampilkan Semua Suku Cadang di Tabel Admin
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Product::with(['category', 'prices']);

        if (!empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('brand', 'LIKE', '%' . $search . '%');
        }

        // Gunakan paginate khusus admin agar load data ribuan sparepart tetap ringan
        $products = $query->latest()->paginate(10);

        return view('admin.products.index', compact('products', 'search'));
    }

    // 2. FORM CREATE: Ambil data kategori untuk dropdown opsi
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    // 3. CREATE PROCESS: Simpan Suku Cadang Baru (Aman Transaksi)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'current_stock' => 'required|integer|min:0',
            'retail_price' => 'required|numeric|min:0',
            'cashback_percent' => 'nullable|integer|min:0|max:100',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        DB::beginTransaction();
        try {
            // A. Simpan data produk dasar
            $product = Product::create([
                'name' => $request->name,
                'brand' => $request->brand,
                'category_id' => $request->category_id,
                'current_stock' => $request->current_stock,
                'cashback_percent' => $request->input('cashback_percent', 0),
            ]);

            // B. Simpan data Harga Retail (Price Level 1)
            Price::create([
                'product_id' => $product->id,
                'price_level' => 1,
                'price' => $request->retail_price
            ]);

            // C. Proses Upload Multi-Foto jika ada gambar yang dimasukkan
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Suku cadang baru berhasil didaftarkan ke gudang!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    // 4. FORM EDIT: Ambil data produk spesifik beserta relasinya
    public function edit($id)
    {
        $product = Product::with(['prices', 'images'])->findOrFail($id);
        $categories = Category::all();
        $retailPrice = $product->prices->where('price_level', 1)->first()->price ?? 0;

        return view('admin.products.edit', compact('product', 'categories', 'retailPrice'));
    }

    // 5. UPDATE PROCESS: Perbarui Data & Kelola Gambar Baru
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'current_stock' => 'required|integer|min:0',
            'retail_price' => 'required|numeric|min:0',
            'cashback_percent' => 'nullable|integer|min:0|max:100',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $product = Product::findOrFail($id);

        DB::beginTransaction();
        try {
            // A. Update info utama produk
            $product->update([
                'name' => $request->name,
                'brand' => $request->brand,
                'category_id' => $request->category_id,
                'current_stock' => $request->current_stock,
                'cashback_percent' => $request->input('cashback_percent', 0),
            ]);

            // B. Update atau buat baru data Harga Level 1
            Price::updateOrCreate(
                ['product_id' => $product->id, 'price_level' => 1],
                ['price' => $request->retail_price]
            );

            // C. Tambah foto baru tanpa menghapus foto lama jika ada upload baru
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Data suku cadang berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    // 6. DELETE: Hapus Produk & Bersihkan File Gambar Fisik dari Storage
    public function destroy($id)
    {
        $product = Product::with('images')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Hapus file fisik gambar dari folder storage agar memori server tidak penuh
            foreach ($product->images as $img) {
                if (Storage::disk('public')->exists($img->image_path)) {
                    Storage::disk('public')->delete($img->image_path);
                }
                $img->delete(); // Hapus baris data di tabel product_images
            }

            // Hapus harga terkait
            Price::where('product_id', $product->id)->delete();

            // Terakhir, hapus data utama produk
            $product->delete();

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Suku cadang telah dihapus permanen dari sistem gudang!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}