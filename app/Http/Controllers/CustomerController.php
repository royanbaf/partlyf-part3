<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductPrice;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Transaction;
use App\Models\Broadcast; 
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Http;



class CustomerController extends Controller
{
    // ==========================================================
    // 1. AREA KATALOG & PRODUK UTAMA
    // ==========================================================

   public function dashboard(Request $request)
    {
        // 1. Panggil relasi prices dan images sekaligus agar loading ngebut
        $query = Product::with(['prices', 'images']);

        // 2. SIHIR PRIORITAS: Lempar produk yang stoknya habis (0) ke urutan paling belakang
        $query->orderByRaw("CASE WHEN current_stock <= 0 THEN 1 ELSE 0 END ASC");

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
                        ->where('price_level', 1) // 1 = Harga Retail B2C
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

  // ==========================================
    // 1. FUNGSI TAMBAH KE KERANJANG
    // ==========================================
    public function addToCart(Request $request, $product_id)
    {
        $product = \App\Models\Product::find($product_id);
        
        if (!$product) {
            return back()->with('error', 'Produk tidak ditemukan.');
        }

        $requestedQty = $request->input('qty', 1);

        // GEMBOK KETAT 1: Cek stok asli produk
        if ($product->current_stock <= 0) {
            return back()->with('error', "Maaf bos, stok {$product->name} sedang habis total! Tidak bisa dimasukkan ke keranjang.");
        }

        // Cek apakah barang sudah ada di keranjang user
        $cartItem = \App\Models\Cart::where('user_id', \Illuminate\Support\Facades\Auth::id())
                                    ->where('product_id', $product_id)
                                    ->first();

        // Jika sudah ada di keranjang, cek apakah total tambahannya melebihi sisa stok
        $existingQty = $cartItem ? $cartItem->qty : 0;
        $newTotalQty = $existingQty + $requestedQty;

        if ($newTotalQty > $product->current_stock) {
            return back()->with('error', "Stok {$product->name} terbatas. Anda sudah punya {$existingQty} di keranjang, dan sisa stok hanya {$product->current_stock} pcs.");
        }

        // Jika semua lolos, simpan ke keranjang
        if ($cartItem) {
            $cartItem->qty += $requestedQty;
            $cartItem->save();
        } else {
            \App\Models\Cart::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'product_id' => $product_id,
                'qty' => $requestedQty
            ]);
        }

        return back()->with('success', 'Berhasil ditambahkan ke keranjang Partlyfe!');
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

    public function checkout()
    {
        // 1. Setup Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        // 2. Simulasi Data Pesanan (Nanti bisa disesuaikan dengan data Keranjang/Cart asli di DB kamu)
        $orderId = 'TRX-' . time(); // Contoh: TRX-1715856742
        $grossAmount = 150000;      // Contoh Total Belanja Rp 150.000

        // 3. Buat Payload untuk dikirim ke Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => \Illuminate\Support\Facades\Auth::user()->name,
                'email' => \Illuminate\Support\Facades\Auth::user()->email,
                'phone' => \Illuminate\Support\Facades\Auth::user()->phone ?? '08111222333',
            ],
        ];

        // 4. Dapatkan Snap Token
        try {
            $snapToken = Snap::getSnapToken($params);
            
            // Tampilkan halaman checkout beserta tokennya
            return view('customer.checkout', [
                'snapToken' => $snapToken,
                'orderId' => $orderId,
                'grossAmount' => $grossAmount
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function initiatePayment(Request $request)
    {
        // Inisialisasi Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $orderId = 'TRX-' . time() . '-' . \Illuminate\Support\Facades\Auth::id();
        $grossAmount = 0;
        $itemDetails = [];
        $dbDetails = []; 

        if ($request->has('product_id')) {
            // PERBAIKAN: Pakai "find" biasa, BUKAN paginate()
            $product = \App\Models\Product::with('prices')->find($request->product_id);
            
            if (!$product) return response()->json(['status' => 'error', 'message' => 'Produk tidak ditemukan.'], 404);
            
            $qty = $request->input('qty', 1);

            // GEMBOK KETAT 2: Cek stok saat Beli Langsung
            if ($product->current_stock < $qty) {
                return response()->json([
                    'status' => 'error', 
                    'message' => "Gagal! Stok {$product->name} tidak cukup. Sisa stok: {$product->current_stock} pcs."
                ], 400);
            }
            
            $retailPriceObj = $product->prices->where('price_level', 1)->first();
            $price = $retailPriceObj ? $retailPriceObj->price : 0;
            if ($price <= 0) return response()->json(['status' => 'error', 'message' => 'Harga produk belum diatur.'], 400);
            
            $itemDetails[] = [
                'id' => $product->id,
                'price' => (int)$price,
                'quantity' => (int)$qty,
                'name' => substr($product->name, 0, 50),
            ];

            // Siapkan data untuk tabel transaction_details
            $dbDetails[] = [
                'product_id' => $product->id,
                'qty' => $qty,
                'price' => $price,
            ];
        } else {
            $cartItems = \App\Models\Cart::where('user_id', \Illuminate\Support\Facades\Auth::id())
                            ->with(['product', 'product.prices'])->get();
            
            if ($cartItems->isEmpty()) return response()->json(['status' => 'error', 'message' => 'Keranjang kosong.'], 400);

            foreach ($cartItems as $item) {
                // GEMBOK KETAT 3: Cek stok untuk setiap barang di keranjang sebelum checkout
                if ($item->product->current_stock < $item->qty) {
                    return response()->json([
                        'status' => 'error', 
                        'message' => "Stok {$item->product->name} tidak cukup untuk pesanan Anda. Sisa stok: {$item->product->current_stock} pcs."
                    ], 400);
                }

                $retailPriceObj = $item->product->prices->where('price_level', 1)->first();
                $price = $retailPriceObj ? $retailPriceObj->price : 0;

                if ($price > 0) {
                    $itemDetails[] = [
                        'id' => $item->product->id,
                        'price' => (int)$price,
                        'quantity' => (int)$item->qty,
                        'name' => substr($item->product->name, 0, 50),
                    ];
                    
                    // Siapkan data untuk tabel transaction_details
                    $dbDetails[] = [
                        'product_id' => $item->product->id,
                        'qty' => $item->qty,
                        'price' => $price,
                    ];
                }
            }
        }

        foreach ($itemDetails as $detail) {
            $grossAmount += ($detail['price'] * $detail['quantity']);
        }

        if ($grossAmount <= 0) return response()->json(['status' => 'error', 'message' => 'Total tagihan Rp 0.'], 400);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int)$grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => \Illuminate\Support\Facades\Auth::user()->name,
                'email' => \Illuminate\Support\Facades\Auth::user()->email,
                'phone' => \Illuminate\Support\Facades\Auth::user()->phone ?? '08123456789',
            ],
        ];

        // PROSES SIMPAN KE DATABASE (MENGGUNAKAN DB TRANSACTION)
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // A. Simpan ke tabel transactions
            $transaction = \App\Models\Transaction::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'invoice_number' => $orderId,
                'total_amount' => $grossAmount,
                'status' => 'pending', 
            ]);

            // B. Simpan ke tabel transaction_details
            foreach ($dbDetails as $detail) {
                \App\Models\TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $detail['product_id'],
                    'qty' => $detail['qty'],
                    'price' => $detail['price'],
                ]);
            }

            // C. Kosongkan keranjang jika checkout berasal dari halaman Keranjang
            if (!$request->has('product_id')) {
                \App\Models\Cart::where('user_id', \Illuminate\Support\Facades\Auth::id())->delete();
            }

            // D. Minta Token Midtrans
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            // Jika semua aman, Permanenkan data di Database
            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken
            ]);

        } catch (\Exception $e) {
            // Jika gagal buat token atau gagal simpan DB, batalkan semua insert data
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ], 500);
        }
    }


   // ==========================================
    // 3. FUNGSI UNTUK MENGUBAH STATUS & POTONG STOK
    // ==========================================
    public function updatePaymentStatus(Request $request)
    {
        $orderId = $request->input('order_id');
        $status = $request->input('transaction_status'); 

        $transaction = \App\Models\Transaction::where('invoice_number', $orderId)->with('details.product')->first();
        
        if ($transaction && $transaction->status == 'pending') {
            
            if ($status == 'settlement' || $status == 'capture') {
                \Illuminate\Support\Facades\DB::beginTransaction();
                try {
                    // 1. Ubah Status
                    $transaction->status = 'processing';
                    $transaction->save();

                    // 2. Potong Stok Fisik Produk
                    foreach ($transaction->details as $detail) {
                        $product = $detail->product;
                        // Pastikan stok tidak minus (walau sudah di-lock sebelumnya)
                        $product->current_stock = max(0, $product->current_stock - $detail->qty);
                        $product->save();
                    }
                    
                    \Illuminate\Support\Facades\DB::commit();
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\DB::rollBack();
                    // Log error jika perlu
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
    
 
    public function invoice($invoice_number)
    {
        // Cari transaksi berdasarkan nomor invoice dan pastikan milik user yang sedang login
        $transaction = \App\Models\Transaction::where('invoice_number', $invoice_number)
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->with(['details.product'])
            ->firstOrFail();

        return view('customer.invoice', compact('transaction'));
    }
    // MENAMPILKAN HALAMAN DETAIL PRODUK & REKOMENDASI REAL AI API
    public function show($id)
    {
        // 1. Ambil produk utama yang sedang di-klik oleh pelanggan
        $product = Product::with(['category', 'prices', 'images'])->findOrFail($id);

        // 2. Ambil katalog produk lain yang stoknya ready sebagai kandidat rekomendasi
        $allProducts = Product::with(['prices', 'images'])
            ->where('id', '!=', $product->id)
            ->where('current_stock', '>', 0)
            ->get();

        // Sederhanakan data katalog agar tidak terlalu besar/overload saat dikirim ke API AI
        $catalogData = $allProducts->map(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'brand' => $p->brand,
                'price' => $p->prices->where('price_level', 1)->first()->price ?? 0,
                'stock' => $p->current_stock
            ];
        })->toArray();

        // 3. PROSES TEMBAK API AI (Menggunakan Gemini API Asli)
        $aiRecommendedIds = [];
        $apiKey = env('GEMINI_API_KEY'); // Membaca kunci dari file .env kamu

        if ($apiKey) {
            try {
                // Buat Prompt (perintah bahasa manusia) yang cerdas untuk AI menganalisis kecocokan mekanis
                $prompt = "Kamu adalah sistem AI Rekomendasi Suku Cadang Pintar untuk toko e-commerce Partlyfe.\n" .
                          "Pelanggan saat ini sedang melihat produk ini: Nama: {$product->name}, Merek: {$product->brand}.\n" .
                          "Analisis secara mekanis otomotif dan pilihlah maksimal 5 produk yang paling keren, cocok, atau relevan untuk dibeli bersamaan dari katalog toko kami berikut ini:\n" .
                          json_encode($catalogData) . "\n\n" .
                          "Berikan respon HANYA dalam bentuk array JSON berisi ID produknya saja tanpa ada kata-kata basa-basi pembuka/penutup, contoh hasil: [3, 7, 12, 15]";

                // Mengirim request HTTP POST langsung ke Google Gemini API
                $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ]
                ]);

                if ($response->successful()) {
                    $aiText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    // Bersihkan teks dari format markdown ```json jika AI bandel memberikannya
                    $aiText = str_replace(['```json', '```', "\n", " "], '', $aiText);
                    $aiRecommendedIds = json_decode($aiText, true) ?? [];
                }
            } catch (\Exception $e) {
                // Jika internet mati atau API bermasalah, biarkan array kosong agar masuk ke sistem cadangan lokal
                $aiRecommendedIds = [];
            }
        }

        // 4. ANTISIPASI (BACKUP SYSTEM): Jika API AI gagal merespon, sistem tidak akan crash/mati
        if (empty($aiRecommendedIds) || !is_array($aiRecommendedIds)) {
            // Backup otomatis: Ambil produk acak dari kategori yang sama di database lokal
            $recommendations = Product::with(['prices', 'images'])
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->inRandomOrder()
                ->take(5)
                ->get();
        } else {
            // Jika API AI sukses merespon, ambil data produk dari DB sesuai urutan ID rekomendasi AI tersebut
            $recommendations = Product::with(['prices', 'images'])
                ->whereIn('id', $aiRecommendedIds)
                ->get()
                ->sortBy(function($model) use ($aiRecommendedIds) {
                    return array_search($model->id, $aiRecommendedIds);
                });
        }

        // 5. Lempar data produk utama dan hasil rekomendasi AI beneran ke file Blade
        return view('customer.product', compact('product', 'recommendations'));
    }

}