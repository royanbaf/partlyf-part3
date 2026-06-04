<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transaksi | Partlyfe Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-dropdown option {
            background-color: #0f172a;
            color: #ffffff;
        }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 flex h-screen overflow-hidden">
    @include('layouts.admin-sidebar')
    
    <main class="flex-1 overflow-y-auto p-10">
        {{-- Header --}}
        <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-white uppercase tracking-tight">Riwayat Transaksi</h2>
                <p class="text-xs text-slate-500">Pusat pemantauan dan audit pesanan masuk pelanggan B2C secara real-time.</p>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="bg-slate-900/40 backdrop-blur-md rounded-3xl border border-white/5 overflow-hidden shadow-2xl">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-slate-400 border-b border-white/5">
                    <tr>
                        <th class="px-6 py-5">Tanggal Masuk</th>
                        <th class="px-6 py-5">Invoice</th>
                        <th class="px-6 py-5">Pelanggan</th>
                        <th class="px-6 py-5">Payment Method</th>
                        <th class="px-6 py-5">Status Order</th>
                        <th class="px-6 py-5 text-right">Total Tagihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-white/[0.02] transition-colors duration-150" data-invoice="{{ $t->invoice_number ?? $t->transaction_real_id }}">
                            
                            <td class="px-6 py-5 text-xs text-slate-400 font-medium">
                                {{ isset($t->created_at) ? date('d M Y, H:i', strtotime($t->created_at)) : '-' }}
                            </td>

                            <td class="px-6 py-5 font-mono text-xs text-indigo-400 font-bold tracking-tight">
                                #{{ $t->invoice_number ?? $t->transaction_real_id }}
                            </td>

                            <td class="px-6 py-5 text-sm text-white font-semibold">
                                {{ $t->customer_name ?? 'Guest User' }}
                            </td>

                            <td class="px-6 py-5 text-xs text-slate-400 font-mono uppercase tracking-wide">
                                {{ $t->payment_method ?? 'MIDTRANS API' }}
                            </td>

                            {{-- BADGE STATUS DINAMIS --}}
                            <td class="px-6 py-5">
                                @php
                                    $currentStatus = strtolower(trim($t->status ?? 'pending'));
                                @endphp

                                <select onchange="updateStatus(this)" data-id="{{ $t->invoice_number ?? $t->transaction_real_id }}" class="status-dropdown appearance-none bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition cursor-pointer pr-10">
                                    <option value="Menunggu Bayar" {{ in_array($currentStatus, ['pending', 'unpaid']) ? 'selected' : '' }}>Menunggu Bayar</option>
                                    <option value="Sedang Diproses" {{ $currentStatus == 'processing' ? 'selected' : '' }}>Sedang Diproses</option>
                                    <option value="Selesai" {{ in_array($currentStatus, ['shipped', 'delivered']) ? 'selected' : '' }}>Selesai</option>
                                    <option value="Dibatalkan" {{ $currentStatus == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </td>

                            <td class="px-6 py-5 text-right font-mono font-black text-white text-sm">
                                Rp {{ number_format($t->total_amount ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-xs text-slate-500 italic">
                                Belum ada berkas transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <script>
        async function updateStatus(selectElement) {
            const id = selectElement.dataset.id;
            const status = selectElement.value;

            try {
                const response = await fetch('{{ route("admin.transactions.updateStatus") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ id: id, status: status })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Update badge visual tanpa reload
                    const row = selectElement.closest('tr');
                    showToast('Status berhasil diupdate!', 'success');
                } else {
                    showToast(data.message || 'Gagal update status', 'error');
                    selectElement.selectedIndex = 0; // Reset dropdown
                }
            } catch (error) {
                showToast('Error: ' + error.message, 'error');
            }
        }

        function showToast(message, type = 'success') {
            let toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-xs font-bold z-50 ${type === 'success' ? 'bg-emerald-500/90 text-white' : 'bg-rose-500/90 text-white'}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    </script>
</body>
</html>