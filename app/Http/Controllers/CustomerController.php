<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductPrice;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Transaction;
use App\Models\Broadcast; // Jangan lupa panggil model Broadcast
use Illuminate\Support\Facades\Auth;


class CustomerController extends Controller
{
    // ==========================================================
    // 1. AREA KATALOG & PRODUK UTAMA
    // ==========================================================

    public function dashboard(Request $request)
    {
        $query = Product::with('prices');

        // Fitur Pencarian (Search)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        // Fitur Filter Kategori
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Fitur Urutkan (Sorting) Harga
        if ($request->has('sort')) {
            if ($request->sort == 'terendah') {
                $query->orderBy(
                    ProductPrice::select('price')
                        ->whereColumn('product_prices.product_id', 'products.id')
                        ->where('price_level', 1)
                        ->limit(1),
                    'asc'
                );
            } elseif ($request->sort == 'tertinggi') {
                $query->orderBy(
                    ProductPrice::select('price')
                        ->whereColumn('product_prices.product_id', 'products.id')
                        ->where('price_level', 1)
                        ->limit(1),
                    'desc'
                );
            }
        } else {
            // Default: Urutkan dari barang terbaru (Relevansi)
            $query->latest();
        }

        $products = $query->paginate(15)->withQueryString(); 
        $categories = Category::all();

        return view('customer.dashboard', compact('products', 'categories'));
    }

    public function productDetail($id)
    {
        $product = Product::with(['prices', 'category'])->findOrFail($id);

        $recommendations = Product::with('prices')
            ->where('id', '!=', $id) 
            ->inRandomOrder()
            ->take(10)
            ->get();

        return view('customer.product', compact('product', 'recommendations'));
    }

    public function allCategories()
    {
        $categories = Category::all();
        return view('customer.categories', compact('categories'));
    }

    // ==========================================================
    // 2. AREA KERANJANG (CART)
    // ==========================================================

    public function cart()
    {
        $cartItems = Cart::with(['product.prices'])->where('user_id', Auth::id())->latest()->get();
        return view('customer.cart', compact('cartItems'));
    }

    public function addToCart(Request $request, $product_id)
    {
        $product = Product::findOrFail($product_id);
        
        // Validasi Stok Kosong
        if ($product->current_stock <= 0) {
            return back()->with('error', 'Maaf, barang sedang habis dan tidak bisa dimasukkan ke keranjang.');
        }

        $qty = $request->input('qty', 1);
        $cart = Cart::where('user_id', Auth::id())->where('product_id', $product_id)->first();

        if ($cart) {
            if (($cart->qty + $qty) > $product->current_stock) {
                return back()->with('error', 'Gagal: Jumlah total melebihi sisa stok yang ada!');
            }
            $cart->qty += $qty;
            $cart->save();
        } else {
            if ($qty > $product->current_stock) {
                return back()->with('error', 'Gagal: Jumlah yang diminta melebihi sisa stok!');
            }
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product_id,
                'qty' => $qty
            ]);
        }

        return back()->with('success', 'Barang berhasil masuk keranjang!');
    }

    public function updateCart(Request $request, $id)
    {
        $cart = Cart::where('user_id', Auth::id())->findOrFail($id);
        
        $qty = $request->qty;
        if($qty > 0 && $qty <= $cart->product->current_stock) {
            $cart->update(['qty' => $qty]);
        }
        
        return back()->with('success', 'Jumlah barang diperbarui.');
    }

    public function removeFromCart($id)
    {
        Cart::where('user_id', Auth::id())->where('id', $id)->delete();
        return back()->with('success', 'Barang berhasil dibuang dari keranjang.');
    }

    // ==========================================================
    // 3. AREA WISHLIST
    // ==========================================================

    public function wishlist()
    {
        $wishlists = Wishlist::with(['product.prices'])->where('user_id', Auth::id())->latest()->get();
        return view('customer.wishlist', compact('wishlists'));
    }

    public function toggleWishlist($product_id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('product_id', $product_id)->first();

        if ($wishlist) {
            $wishlist->delete();
            return back()->with('success', 'Dihapus dari Wishlist!');
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $product_id
            ]);
            return back()->with('success', 'Ditambahkan ke Wishlist!');
        }
    }

    // ==========================================================
    // 4. AREA TRANSAKSI & PENGATURAN USER
    // ==========================================================

    public function transactions()
    {
        // Mengambil riwayat transaksi user beserta rincian barangnya
        $transactions = Transaction::with('details.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
            
        return view('customer.transactions', compact('transactions'));
    }

    // ==========================================================
    // 5. AREA KABAR ADMIN (BROADCAST)
    // ==========================================================

    public function broadcast()
    {
        // Ambil pesan dari DB, yang terbaru di atas
        $broadcasts = Broadcast::where('user_id', Auth::id())->latest()->get();
        return view('customer.broadcast', compact('broadcasts'));
    }

    // Fungsi canggih untuk mengubah status SEMUA pesan menjadi "Terbaca"
    public function markAllBroadcastsRead()
    {
        Broadcast::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    // Fungsi untuk menandai SATU pesan menjadi terbaca saat diklik
    public function markSingleBroadcastRead($id)
    {
        $broadcast = Broadcast::where('user_id', Auth::id())->findOrFail($id);
        $broadcast->update(['is_read' => true]);
        
        return back()->with('success', 'Pesan telah dibaca.');
    }
    public function profile()
    {
        return view('customer.profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        $user = \App\Models\User::find(Auth::id());
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        return back()->with('success', 'Profil dan alamat berhasil diperbarui!');
    }

    public function aiChat()
    {
        return view('customer.ai-chat');
    }

   public function sendAiMessage(Request $request)
    {
        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        // 1. Cek apakah API Key terbaca
        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'reply' => 'Sistem AI Offline: API Key belum terbaca di file .env!'
            ]);
        }

        try {
           $systemPrompt = "Kamu adalah seorang asisten mekanik virtual yang pintar, ramah, dan gaul dari PartLyfe. Jawablah pertanyaan pelanggan dengan singkat, padat, dan berikan solusi seputar otomotif roda dua atau sparepart. Gunakan bahasa Indonesia yang santai tapi profesional, oh iya ini juga untuk pengujian jadi sapa dulu dosen saya bernama pak satria dan pak yustus ,serta asdos saya ruby dan amanda. dan kamu hanya boleh menjawab seputar otomotive sepeda motor saja selain itu bilang maaf saya tidak bisa menjaangkau pertanyaan di luar topik saya  Pertanyaan pelanggan: ";
            
            $fullPrompt = $systemPrompt . $userMessage;

            // PERUBAHAN DI SINI: Kita panggil nama model terbaru gemini-2.5-flash
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $fullPrompt]
                        ]
                    ]
                ]
            ]);
            $data = $response->json();
            
            // 3. Tangkap pesan error ASLI dari Google (Misal: API Key tidak valid)
            if (isset($data['error'])) {
                return response()->json([
                    'status' => 'error',
                    'reply' => 'ERROR DARI GOOGLE: ' . $data['error']['message']
                ]);
            }

            $aiReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Waduh, mekanik AI lagi bengong nih.";
            $aiReplyHtml = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $aiReply);

            return response()->json([
                'status' => 'success',
                'reply' => nl2br($aiReplyHtml)
            ]);

        } catch (\Exception $e) {
            // 4. Tangkap error sistem Laravel
            return response()->json([
                'status' => 'error',
                'reply' => 'ERROR SISTEM: ' . $e->getMessage()
            ]);
        }
    }
}