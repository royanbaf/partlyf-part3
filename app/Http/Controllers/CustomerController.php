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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Midtrans\Config;
use Midtrans\Snap;

class CustomerController extends Controller
{
    // ==========================================================
    // 1. AREA KATALOG & PRODUK UTAMA
    // ==========================================================

    public function dashboard(Request $request)
    {
        // 1. Ambil semua kategori untuk menu filter di bagian atas
        $categories = Category::all();

        // 2. Ambil query pencarian/filter dari URL
        $search = $request->input('search');

        // 3. Bangun query dasar: Ambil produk yang berelasi dengan harga dan gambar
        $productsQuery = Product::with(['prices', 'images', 'category']);
        
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

        // 🚀 SUNTIKAN KUNCI SINKRONISASI NOTIFIKASI LONCENG
        $hasNotification = Broadcast::where('type', 'promo')
            ->where('is_read', false)
            ->exists();

        // 5. Lempar semua variabel dengan selamat ke view dashboard
        return view('customer.dashboard', compact('categories', 'products', 'search', 'hasNotification'));
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
        $product = Product::find($product_id);
        
        if (!$product) {
            return back()->with('error', 'Produk tidak ditemukan.');
        }

        $requestedQty = $request->input('qty', 1);

        // GEMBOK KETAT 1: Cek stok asli produk
        if ($product->current_stock <= 0) {
            return back()->with('error', "Maaf bos, stok {$product->name} sedang habis total! Tidak bisa dimasukkan ke keranjang.");
        }

        // Cek apakah barang sudah ada di keranjang user
        $cartItem = Cart::where('user_id', Auth::id())
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
            Cart::create([
                'user_id' => Auth::id(),
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

    public function transactions(Request $request)
    {
        // 1. Ambil parameter filter status dari URL (contoh: ?status=menunggu)
        $statusFilter = $request->query('status');

        // 2. Bangun query dasar transaksi milik user yang login beserta rincian itemnya
        $query = Transaction::with('details.product')
            ->where('user_id', Auth::id());

        // 3. 🧠 SINKRONISASI FILTER TAB BAHASA INDONESIA DENGAN DROPDOWN ADMIN
        if ($statusFilter) {
            if ($statusFilter == 'menunggu') {
                $query->whereIn('status', ['Menunggu Pembayaran', 'pending', 'unpaid', 'menunggu']);
            } elseif ($statusFilter == 'diproses') {
                $query->whereIn('status', ['Sedang Diproses', 'processing', 'diproses']);
            } elseif ($statusFilter == 'selesai') {
                $query->whereIn('status', ['Selesai', 'success', 'settlement']);
            } elseif ($statusFilter == 'gagal') {
                $query->whereIn('status', ['Dibatalkan', 'expire', 'cancel', 'gagal']);
            } else {
                $query->where('status', $statusFilter);
            }
        }

        // 4. 🔥 EKSEKUSI DENGAN PAGINATION
        $transactions = $query->latest('created_at')->paginate(10);
            
        return view('customer.transactions', compact('transactions', 'statusFilter'));
    }
    
    // ==========================================================
    // 5. AREA KABAR ADMIN (BROADCAST) - VERSI SINKRON 100%
    // ==========================================================
    public function broadcast()
    {
        // 🚀 SINKRONISASI UTUH: Saring berdasarkan type 'promo' kiriman admin
        $broadcasts = Broadcast::where('type', 'promo')->latest()->get();
        
        // Otomatisasi: Begitu halaman dibuka, tandai semua pesan tipe promo menjadi terbaca
        Broadcast::where('type', 'promo')->where('is_read', false)->update(['is_read' => true]);

        return view('customer.broadcast', compact('broadcasts'));
    }

    public function markAllBroadcastsRead()
    {
        Broadcast::where('type', 'promo')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    public function markSingleBroadcastRead($id)
    {
        $broadcast = Broadcast::findOrFail($id);
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

    // ==========================================================
    // 6. ASISTEN MEKANIK VIRTUAL (GEMINI AI CHAT)
    // ==========================================================
    public function aiChat()
    {
        return view('customer.ai-chat');
    }

    public function sendAiMessage(Request $request)
    {
        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'reply' => 'Sistem AI Offline: API Key belum terbaca di file .env!'
            ]);
        }

        try {
            $systemPrompt = "Kamu adalah seorang asisten mekanik virtual yang pintar, ramah, dan gaul dari PartLyfe. Jawablah pertanyaan pelanggan dengan singkat, padat, dan berikan solusi seputar otomotif roda dua atau sparepart. Gunakan bahasa Indonesia yang santai tapi profesional,dan kamu hanya boleh menjawab seputar otomotive sepeda motor saja selain itu billing maaf saya tidak bisa menjangkau pertanyaan di luar topik saya. Pertanyaan pelanggan: ";
            
            $fullPrompt = $systemPrompt . $userMessage;

            $response = Http::withoutVerifying()->withHeaders([
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
            
            if (isset($data['error'])) {
                return response()->json([
                    'status' => 'error',
                    'reply' => 'ERROR DARI GOOGLE: ' . $data['error']['message']
                ]);
            }

            $aiReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Waduh, mekanik AI lagi bengong nih.";
            $aiReplyHtml = preg_replace('/\*\*(.*?)\*\//', '<strong>$1</strong>', $aiReply);

            return response()->json([
                'status' => 'success',
                'reply' => nl2br($aiReplyHtml)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'reply' => 'ERROR SISTEM: ' . $e->getMessage()
            ]);
        }
    }

    // ==========================================================
    // 7. AREA PROCESS CHECKOUT & TRANSAKSI MIDTRANS
    // ==========================================================
    public function checkout(Request $request)
    {
        $productId = $request->query('product_id');
        $qty = $request->query('qty', 1);

        $checkoutItems = [];
        $subtotal = 0;

        if ($productId) {
            // JALUR 1: Beli Langsung
            $product = Product::with(['prices', 'images'])->findOrFail($productId);
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
            $cartItems = Cart::with(['product.prices', 'product.images'])->where('user_id', Auth::id())->get();
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

        // Biaya Tambahan
        $ongkosKirim = 26000;
        $asuransi = 38200;
        $biayaProteksi = 65000;
        $biayaLayanan = 2000; 
        $totalTagihan = $subtotal + $ongkosKirim + $asuransi + $biayaProteksi + $biayaLayanan;

        return view('customer.checkout_summary', compact(
            'checkoutItems', 'subtotal', 'ongkosKirim', 'asuransi', 'biayaProteksi', 'biayaLayanan', 'totalTagihan', 'productId', 'qty'
        ));
    }

    public function initiatePayment(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $orderId = 'TRX-' . time() . '-' . Auth::id();
        $grossAmount = 0;
        $itemDetails = [];
        $dbDetails = []; 

        if ($request->has('product_id')) {
            $product = Product::with('prices')->find($request->product_id);
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
            $cartItems = Cart::where('user_id', Auth::id())->with(['product', 'product.prices'])->get();
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

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int)$grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone ?? '08123456789',
            ],
            'expiry' => [
                'start_time' => date("Y-m-d H:i:s O"),
                'unit' => 'minute',
                'duration' => 1440 
            ],
        ];

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
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
                Cart::where('user_id', Auth::id())->delete();
            }

            $snapToken = Snap::getSnapToken($params);
            $transaction->snap_token = $snapToken;
            $transaction->save();
            
            DB::commit();

            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePaymentStatus(Request $request)
    {
        $orderId = $request->input('order_id');
        $status = $request->input('transaction_status'); 

        $transaction = Transaction::where('invoice_number', $orderId)
            ->with('details.product')
            ->first();
        
        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor Invoice ' . $orderId . ' tidak ditemukan!'
            ], 404);
        }

        if (in_array($status, ['settlement', 'capture', 'success', 'processing'])) {
            if (in_array($transaction->status, ['pending', 'expire', 'cancel', 'gagal'])) {
                DB::beginTransaction();
                try {
                    $transaction->status = 'processing';
                    $transaction->save();

                    foreach ($transaction->details as $detail) {
                        $product = $detail->product;
                        if ($product) {
                            $product->current_stock = max(0, $product->current_stock - $detail->qty);
                            $product->save();
                        }
                    }
                    
                    DB::commit();
                    return response()->json(['status' => 'success', 'message' => 'Pembayaran Berhasil! Stok dipotong.']);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
                }
            } else {
                return response()->json(['status' => 'success', 'message' => 'Transaksi sudah diproses.']);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Status diterima: ' . $status]);
    }
    
    public function invoice($invoice_number)
    {
        $transaction = Transaction::where('invoice_number', $invoice_number)
            ->where('user_id', Auth::id())
            ->with(['details.product'])
            ->firstOrFail();

        return view('customer.invoice', compact('transaction'));
    }

    // ==========================================================
    // 8. AREA REKOMENDASI SMART GENERATIVE AI PRO
    // ==========================================================
    public function show($id)
    {
        $product = Product::with(['category', 'prices', 'images'])->findOrFail($id);

        $allProducts = Product::with(['prices', 'images'])
            ->where('id', '!=', $product->id)
            ->where('current_stock', '>', 0)
            ->get();

        $catalogData = $allProducts->map(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'brand' => $p->brand,
                'price' => $p->prices->where('price_level', 1)->first()->price ?? 0,
                'stock' => $p->current_stock
            ];
        })->toArray();

        $aiRecommendedIds = [];
        $apiKey = env('GEMINI_API_KEY');

        if ($apiKey) {
            try {
                $prompt = "Kamu adalah sistem AI Rekomendasi Suku Cadang Pintar untuk toko e-commerce Partlyfe.\n" .
                          "Pelanggan saat ini sedang melihat produk ini: Nama: {$product->name}, Merek: {$product->brand}.\n" .
                          "Analisis secara mekanis otomotif dan pilihlah maksimal 5 produk yang paling keren, cocok, atau relevan untuk dibeli bersamaan dari katalog toko kami berikut ini:\n" .
                          json_encode($catalogData) . "\n\n" .
                          "Berikan respon HANYA dalam bentuk array JSON berisi ID produknya saja tanpa ada kata-kata basa-basi pembuka/penutup, contoh hasil: [3, 7, 12, 15]";

                $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ]
                ]);

                if ($response->successful()) {
                    $aiText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    $aiText = str_replace(['```json', '```', "\n", " "], '', $aiText);
                    $aiRecommendedIds = json_decode($aiText, true) ?? [];
                }
            } catch (\Exception $e) {
                $aiRecommendedIds = [];
            }
        }

        if (empty($aiRecommendedIds) || !is_array($aiRecommendedIds)) {
            $recommendations = Product::with(['prices', 'images'])
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->inRandomOrder()
                ->take(5)
                ->get();
        } else {
            $recommendations = Product::with(['prices', 'images'])
                ->whereIn('id', $aiRecommendedIds)
                ->get()
                ->sortBy(function($model) use ($aiRecommendedIds) {
                    return array_search($model->id, $aiRecommendedIds);
                });
        }

        return view('customer.product', compact('product', 'recommendations'));
    }

    // ==========================================================
    // 9. AREA INTELLIGENT SEARCH (Mekanik AI Keluhan Kerusakan)
    // ==========================================================
    public function aiSearch(Request $request)
    {
        $keluhan = $request->input('q');
        if (empty($keluhan)) {
            return response()->json(['status' => 'success', 'interpreted_as' => '', 'explanation' => '', 'data' => []]);
        }

        $apiKey = env('GEMINI_API_KEY');

        try {
            $prompt = "Anda adalah sistem AI perantara untuk pencarian produk di database e-commerce suku cadang motor Sinar Jaya Motor.\n\n"
                    . "Tugas Anda adalah menerima keluhan kerusakan dari pelanggan awam, lalu menganalisisnya secara mekanis, kemudian menghasilkan kata kunci pencarian (keywords) produk pendukungnya yang spesifik agar bisa dicari di database SQL menggunakan perintah LIKE.\n\n"
                    . "Contoh Input Keluhan: 'oli rusak'\n"
                    . "Contoh Output JSON: {\"keywords\": [\"Oli\", \"Mesin\", \"Gardan\", \"Yamalube\", \"MPX\"], \"explanation\": \"Oli yang rusak atau menghitam harus segera diganti untuk menghindari gesekan kasar pada mesin dan transmisi gardan matic Anda.\"}\n\n"
                    . "Contoh Input Keluhan: 'rem blong'\n"
                    . "Contoh Output JSON: {\"keywords\": [\"Kampas\", \"Rem\", \"Minyak\", \"Master\", \"Cakram\"], \"explanation\": \"Rem blong biasanya disebabkan oleh kampas rem yang telah aus atau adanya gelembung udara di dalam saluran minyak rem.\"}\n\n"
                    . "Sekarang, analisis keluhan berikut ini:\n"
                    . "Keluhan: '{$keluhan}'\n\n"
                    . "WAJIB BALAS HANYA DENGAN FORMAT JSON MURNI TANPA MARKDOWN. DILARANG MENGGUNAKAN BLOCK TEMPLATE CODE CODES JASON MAUPUN BACKTICKS. Struktur wajib: {\"keywords\": [], \"explanation\": \"\"}";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);

            $geminiData = $response->json();
            $textResponse = $geminiData['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $textResponse = str_replace(['```json', '```'], '', $textResponse);
            
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

        $query = Product::with('prices');

        if (!empty($aiKeywords)) {
            $query->where(function($q) use ($aiKeywords) {
                foreach ($aiKeywords as $keyword) {
                    $q->orWhere('name', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('brand', 'LIKE', '%' . $keyword . '%');
                }
            });
        }

        $products = $query->take(8)->get();

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