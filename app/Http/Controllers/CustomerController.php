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

   public function dashboard(\Illuminate\Http\Request $request)
    {
        // 1. Ambil semua kategori untuk menu filter di bagian atas
        $categories = \App\Models\Category::all();

        // 2. Ambil query pencarian/filter dari URL
        $search = $request->input('search');

        // 3. Bangun query dasar: Ambil produk yang stoknya ready
        $productsQuery = \App\Models\Product::with(['prices', 'images', 'category']);
        // 🧠 LOGIKA PINTAR AI FILTER & SEARCH
        if ($search) {
            $productsQuery->where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('brand', 'LIKE', "%{$search}%")
                    ->orWhere('item_code', 'LIKE', "%{$search}%")
                    // JURUS KUNCI: Cari juga ke dalam tabel 'categories' yang berelasi
                    ->orWhereHas('category', function($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // 4. Ambil data produk dengan pagination (12 produk per halaman)
        $products = $productsQuery->latest()->paginate(12);

        // 5. Lempar semua variabel dengan selamat ke view dashboard
        return view('customer.dashboard', compact('categories', 'products', 'search'));
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

   public function transactions(\Illuminate\Http\Request $request)
{
    // 1. Ambil parameter filter status dari URL (contoh: ?status=menunggu)
    $statusFilter = $request->query('status');

    // 2. Bangun query dasar transaksi milik user yang login
    $query = \App\Models\Transaction::with('details.product')
        ->where('user_id', \Illuminate\Support\Facades\Auth::id());

    // 3. 🧠 LOGIKA FILTER PINTAR
    if ($statusFilter) {
        if ($statusFilter == 'menunggu') {
            // Filter untuk transaksi yang belum dibayar
            $query->whereIn('status', ['pending', 'unpaid', 'menunggu']);
        } elseif ($statusFilter == 'diproses') {
            // Filter untuk transaksi lunas yang sedang disiapkan / diproses
            $query->where('status', 'processing');
        } elseif ($statusFilter == 'gagal') {
            // Filter untuk transaksi hangus atau dibatalkan
            $query->whereIn('status', ['expire', 'cancel', 'gagal']);
        } else {
            // Filter fallback jika ada status kustom lain (misal: 'dikirim', 'selesai')
            $query->where('status', $statusFilter);
        }
    }

    // 4. 🔥 EKSEKUSI DENGAN PAGINATION (Ubah get() menjadi paginate())
    // Angka 10 berarti menampilkan 10 transaksi per halaman, silakan disesuaikan
    $transactions = $query->latest()->paginate(10);
        
    // 5. Kirim data transaksinya beserta status aktif saat ini ke Blade
    return view('customer.transactions', compact('transactions', 'statusFilter'));
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
           $systemPrompt = "Kamu adalah seorang asisten mekanik virtual yang pintar, ramah, dan gaul dari PartLyfe. Jawablah pertanyaan pelanggan dengan singkat, padat, dan berikan solusi seputar otomotif roda dua atau sparepart. Gunakan bahasa Indonesia yang santai tapi profesional,dan kamu hanya boleh menjawab seputar otomotive sepeda motor saja selain itu bilang maaf saya tidak bisa menjaangkau pertanyaan di luar topik saya  ,Pertanyaan pelanggan: ";
            
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

    
    public function checkout(\Illuminate\Http\Request $request)
    {
        $productId = $request->query('product_id');
        $qty = $request->query('qty', 1);

        $checkoutItems = [];
        $subtotal = 0;

        if ($productId) {
            // JALUR 1: Beli Langsung dari halaman Produk
            $product = \App\Models\Product::with(['prices', 'images'])->findOrFail($productId);
            $price = $product->prices->where('price_level', 1)->first()->price ?? 0;
            
            $checkoutItems[] = (object)[
                'id' => $product->id,
                'name' => $product->name,
                'brand' => $product->brand,
                'image' => $product->images->first() ? asset('storage/products/' . basename($product->images->first()->image_path)) : null,
                'qty' => $qty,
                'price' => $price,
                'subtotal' => $price * $qty
            ];
            $subtotal += $price * $qty;
        } else {
            // JALUR 2: Checkout dari Keranjang
            $cartItems = \App\Models\Cart::with(['product.prices', 'product.images'])->where('user_id', \Illuminate\Support\Facades\Auth::id())->get();
            if ($cartItems->isEmpty()) {
                return redirect()->route('customer.cart')->with('error', 'Keranjang Anda kosong.');
            }
            
            foreach ($cartItems as $item) {
                $price = $item->product->prices->where('price_level', 1)->first()->price ?? 0;
                $checkoutItems[] = (object)[
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'brand' => $item->product->brand,
                    'image' => $item->product->images->first() ? asset('storage/products/' . basename($item->product->images->first()->image_path)) : null,
                    'qty' => $item->qty,
                    'price' => $price,
                    'subtotal' => $price * $item->qty
                ];
                $subtotal += $price * $item->qty;
            }
        }

        // Simulasi Biaya Tambahan (Ala Tokopedia)
        $ongkosKirim = 26000;
        $asuransi = 38200;
        $biayaProteksi = 65000;
        $biayaLayanan = 2000; // Layanan + Jasa Aplikasi
        $totalTagihan = $subtotal + $ongkosKirim + $asuransi + $biayaProteksi + $biayaLayanan;

        return view('customer.checkout_summary', compact(
            'checkoutItems', 'subtotal', 'ongkosKirim', 'asuransi', 'biayaProteksi', 'biayaLayanan', 'totalTagihan', 'productId', 'qty'
        ));
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
            $product = \App\Models\Product::with('prices')->find($request->product_id);
            
            if (!$product) return response()->json(['status' => 'error', 'message' => 'Produk tidak ditemukan.'], 404);
            
            $qty = $request->input('qty', 1);

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

        // 🚀 SETTING PARAMETER MIDTRANS DENGAN EXPIRY WAKTU
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
            // 👇 TAMBAHAN: Aturan kedaluwarsa (Expired) dari Midtrans
            'expiry' => [
                'start_time' => date("Y-m-d H:i:s O"),
                'unit' => 'minute',
                'duration' => 1440 // 1440 menit = 24 Jam. Silakan diubah misal jadi 120 (2 Jam) jika ingin lebih cepat hangus.
            ],
        ];

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $transaction = \App\Models\Transaction::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'invoice_number' => $orderId,
                'total_amount' => $grossAmount,
                'status' => 'pending', 
            ]);

            foreach ($dbDetails as $detail) {
                \App\Models\TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $detail['product_id'],
                    'qty' => $detail['qty'],
                    'price' => $detail['price'],
                ]);
            }

            if (!$request->has('product_id')) {
                \App\Models\Cart::where('user_id', \Illuminate\Support\Facades\Auth::id())->delete();
            }

            // Minta Token Midtrans
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            // menyimpan token ke database agar bisa di-load ulang di halaman Transaksi
            $transaction->snap_token = $snapToken;
            $transaction->save();
            
            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken
            ]);

        } catch (\Exception $e) {
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

        // Cari transaksi berdasarkan nomor invoice beserta detail produknya
        $transaction = \App\Models\Transaction::where('invoice_number', $orderId)
            ->with('details.product')
            ->first();
        
        // Proteksi 1: Jika nomor invoice gadungan / tidak ditemukan
        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor Invoice ' . $orderId . ' tidak ditemukan di database!'
            ], 404);
        }

        // Cek apakah status yang dikirim dari Midtrans/Frontend bernilai sukses
        if ($status == 'settlement' || $status == 'capture' || $status == 'success' || $status == 'processing') {
            
            // Proteksi 2: Gembok kebal bug. Izinkan update jika status DB adalah pending, expire, atau cancel.
            // Ini agar jika transaksi sempat hangus karena salah timezone, bisa dihidupkan kembali saat lunas!
            if (in_array($transaction->status, ['pending', 'expire', 'cancel', 'gagal'])) {
                
                \Illuminate\Support\Facades\DB::beginTransaction();
                try {
                    // 1. Ubah Status ke 'processing' sesuai kebutuhan bisnismu
                    $transaction->status = 'processing';
                    $transaction->save();

                    // 2. Potong Stok Fisik Produk
                    foreach ($transaction->details as $detail) {
                        $product = $detail->product;
                        if ($product) {
                            $product->current_stock = max(0, $product->current_stock - $detail->qty);
                            $product->save();
                        }
                    }
                    
                    \Illuminate\Support\Facades\DB::commit();
                    
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Pembayaran Berhasil! Stok barang telah dipotong.'
                    ]);

                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal memproses perubahan database: ' . $e->getMessage()
                    ], 500);
                }
            } else {
                // Jika statusnya sudah 'processing', biarkan saja (berarti sudah lunas sebelumnya)
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaksi sudah berstatus ' . $transaction->status
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Status diterima: ' . $status
        ]);
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
    public function aiSearch(\Illuminate\Http\Request $request)
{
    $keluhan = $request->input('q');
    if (empty($keluhan)) {
        return response()->json(['status' => 'success', 'interpreted_as' => '', 'explanation' => '', 'data' => []]);
    }

    $apiKey = env('GEMINI_API_KEY');

    try {
        // 🧠 PROMPT ENGINEERING UTUH DAN BERSIH
        $prompt = "Anda adalah sistem AI perantara untuk pencarian produk di database e-commerce suku cadang motor Sinar Jaya Motor.\n\n"
                . "Tugas Anda adalah menerima keluhan kerusakan dari pelanggan awam, lalu menganalisisnya secara mekanis, kemudian menghasilkan kata kunci pencarian (keywords) produk pendukungnya yang spesifik agar bisa dicari di database SQL menggunakan perintah LIKE.\n\n"
                . "Contoh Input Keluhan: 'oli rusak'\n"
                . "Contoh Output JSON: {\"keywords\": [\"Oli\", \"Mesin\", \"Gardan\", \"Yamalube\", \"MPX\"], \"explanation\": \"Oli yang rusak atau menghitam harus segera diganti untuk menghindari gesekan kasar pada mesin dan transmisi gardan matic Anda.\"}\n\n"
                . "Contoh Input Keluhan: 'rem blong'\n"
                . "Contoh Output JSON: {\"keywords\": [\"Kampas\", \"Rem\", \"Minyak\", \"Master\", \"Cakram\"], \"explanation\": \"Rem blong biasanya disebabkan oleh kampas rem yang telah aus atau adanya gelembung udara di dalam saluran minyak rem.\"}\n\n"
                . "Sekarang, analisis keluhan berikut ini:\n"
                . "Keluhan: '{$keluhan}'\n\n"
                . "WAJIB BALAS HANYA DENGAN FORMAT JSON MURNI TANPA MARKDOWN. DILARANG MENGGUNAKAN BLOCK TEMPLATE CODE CODES JASON MAUPUN BACKTICKS. Struktur wajib: {\"keywords\": [], \"explanation\": \"\"}";

        // Tembak API Gemini via Guzzle HTTP Laravel
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ]);

        $geminiData = $response->json();
        $textResponse = $geminiData['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        // Bersihkan sisa-sisa markdown membandel jika Gemini melanggar aturan prompt
        $textResponse = str_replace(['```json', '```'], '', $textResponse);
        
        // Gunakan Regex untuk mengambil string JSON di dalam kurung kurawal {...}
        preg_match('/\{.*\}/s', $textResponse, $matches);
        $cleanJson = $matches[0] ?? '{}';
        
        $parsedAi = json_decode($cleanJson, true);

        if (is_array($parsedAi) && isset($parsedAi['keywords'])) {
            $aiKeywords = $parsedAi['keywords'];
            $explanation = $parsedAi['explanation'];
        } else {
            $aiKeywords = explode(' ', $keluhan);
            $explanation = "Mekanik AI menyarankan Anda untuk memeriksa komponen yang berkaitan dengan keluhan Anda.";
        }

    } catch (\Exception $e) {
        $aiKeywords = explode(' ', $keluhan);
        $explanation = "Sistem AI sedang offline, menampilkan hasil pencarian kata kunci standar.";
    }

    // =========================================================
    // JALUR QUERY DATABASE BERDASARKAN HASIL PEMIKIRAN GEMINI
    // =========================================================
    $query = \App\Models\Product::with('prices');

    if (!empty($aiKeywords)) {
        $query->where(function($q) use ($aiKeywords) {
            foreach ($aiKeywords as $keyword) {
                $q->orWhere('name', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('brand', 'LIKE', '%' . $keyword . '%');
            }
        });
    }

    $products = $query->take(8)->get();

    // Format ulang data untuk dikirim balik ke JavaScript frontend dashboard
    $results = $products->map(function($prod) {
        $price = $prod->prices->where('price_level', 1)->first();
        $image = $prod->images->first() ? asset('storage/products/' . basename($prod->images->first()->image_path)) : null;
        
        return [
            'id' => $prod->id,
            'name' => $prod->name,
            'brand' => $prod->brand,
            'price' => number_format($price->price ?? 0, 0, ',', '.'),
            'image' => $image,
            'url' => route('product.detail', $prod->id)
        ];
    });

    return response()->json([
        'status' => 'success',
        'interpreted_as' => is_array($aiKeywords) ? implode(', ', array_unique($aiKeywords)) : $keluhan,
        'explanation' => $explanation,
        'data' => $results
    ]);
}

}