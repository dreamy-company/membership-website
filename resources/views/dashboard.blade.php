<div>
    {{-- 1. HEADER SECTION --}}
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <x-dashboard.breadcrumbs title="Dashboard" />
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Hello, {{ auth()->user()->name }} 👋
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Berikut adalah ringkasan aktivitas dan jaringan Anda.
            </p>
        </div>
    </div>

    {{-- 2. CARDS, STATS & NETWORK GRID --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
        
        {{-- ========================================== --}}
        {{-- CARD 1: E-MEMBER CARD (DESAIN PLATINUM) --}}
        {{-- ========================================== --}}
        <div class="flex flex-col">
            <div class="bg-white p-4 rounded-2xl border border-neutral-200 shadow-sm dark:bg-neutral-900 dark:border-neutral-700">
                
                {{-- Label No Kartu (Di luar area target download) --}}
                <div class="mb-3 px-1">
                    <p class="text-xs text-gray-500 dark:text-gray-400">No. Kartu Member</p>
                    <p class="font-bold text-gray-800 dark:text-white tracking-widest text-sm">
                        {{ auth()->user()->member->member_code ?? 'XXXX - XXXX - XXXX' }}
                    </p>
                </div>

                {{-- DESAIN KARTU UTAMA (PLATINUM GRADIENT) --}}
                {{-- KUNCI: Pindahkan ID 'member-card-element' ke sini agar cuma kartu ini yang ter-foto --}}
                <div id="member-card-element" class="relative w-full aspect-[1.58/1] rounded-xl overflow-hidden shadow-md" 
                     style="background: linear-gradient(135deg, #e5e7eb 0%, #9ca3af 50%, #4b5563 100%);">
                    
                    {{-- Efek Geometris Background (Lebih halus untuk tema Platinum) --}}
                    <div class="absolute inset-0 opacity-30 pointer-events-none">
                        <div class="absolute top-[-20%] left-[-10%] w-32 h-32 bg-white rounded-[40%] transform rotate-12 mix-blend-overlay"></div>
                        <div class="absolute top-[20%] left-[30%] w-24 h-24 bg-white rounded-full mix-blend-overlay opacity-50"></div>
                        <div class="absolute bottom-[-10%] right-[10%] w-40 h-40 bg-black rounded-full mix-blend-overlay opacity-20"></div>
                        {{-- Tekstur garis halus diagonal --}}
                        <div class="absolute inset-0" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px);"></div>
                    </div>

                    <div class="relative h-full p-5 flex flex-col justify-between z-10 text-gray-900">
                        
                        {{-- Top Section: Logo & Tipe Kartu --}}
                        <div class="flex justify-between items-start">
                            <div class="flex items-baseline gap-1">
                                <span class="font-bold italic text-xl drop-shadow-sm text-gray-800">Platinum</span>
                                <span class="text-xs font-medium text-gray-600">Member</span>
                            </div>
                            <div class="font-black text-lg italic drop-shadow-sm tracking-wider text-gray-800">
                                Membership
                            </div>
                        </div>

                        {{-- Middle Section: Chip, QR & Contactless --}}
                        <div class="flex justify-between items-center mt-2">
                            <div class="flex items-center gap-2">
                                {{-- Panah Kecil --}}
                                <div class="w-0 h-0 border-t-[5px] border-t-transparent border-r-[8px] border-r-gray-700 border-b-[5px] border-b-transparent opacity-80"></div>
                                {{-- Simbol Chip (Silver/Platinum Style) --}}
                                <div class="w-10 h-8 rounded bg-gradient-to-br from-gray-100 to-gray-400 border border-gray-500/50 flex flex-col justify-evenly px-1 shadow-inner">
                                    <div class="w-full h-[1px] bg-gray-500/40"></div>
                                    <div class="w-full h-[1px] bg-gray-500/40"></div>
                                </div>
                            </div>
                            
                            {{-- Icon Contactless (Warna Gelap) --}}
                            <div class="flex flex-col gap-[3px] rotate-90 opacity-70">
                                <div class="w-1 h-1 bg-gray-800 rounded-full"></div>
                                <div class="w-2 h-1 bg-gray-800 rounded-full"></div>
                                <div class="w-3 h-1 bg-gray-800 rounded-full"></div>
                                <div class="w-4 h-1 bg-gray-800 rounded-full"></div>
                            </div>
                        </div>

                        {{-- Bottom Section: Nama, QR Code & Logo Sudut --}}
                        <div class="flex justify-between items-end mt-auto">
                            <div class="font-bold text-sm tracking-widest uppercase drop-shadow-sm max-w-[55%] leading-tight text-gray-900">
                                {{ auth()->user()->name }}
                            </div>
                            
                            <div class="flex gap-3 items-end">
                                {{-- Logo Bulatan (Mirip Mastercard) --}}
                                <div class="relative w-10 h-6 mb-1">
                                    <div class="absolute left-0 w-6 h-6 bg-gray-800 rounded-full opacity-80 mix-blend-multiply"></div>
                                    <div class="absolute right-0 w-6 h-6 bg-gray-400 rounded-full opacity-80 mix-blend-multiply"></div>
                                </div>
                                
                                {{-- Kotak Putih untuk Canvas QR Code JS --}}
                                <div class="bg-white p-1.5 rounded-lg shadow-sm flex items-center justify-center border border-gray-300">
                                    <img id="qrcode" class="w-25 h-25 object-contain" alt="QR Code">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

           {{-- Tombol Print Kartu --}}
            <button onclick="printCard()" class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl border border-gray-600 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:text-gray-900 transition hover:bg-gray-100 dark:border-gray-500 dark:text-gray-300 dark:hover:bg-gray-800 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Kartu (Print)
            </button>
        </div>


        {{-- ========================================== --}}
        {{-- CARD 2: BONUS WALLET --}}
        {{-- ========================================== --}}
        <div class="relative flex flex-col justify-between overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">
                            Sisa Bonus Tersedia
                        </p>
                        <h3 class="mt-2 text-3xl font-bold tracking-tight text-neutral-900 dark:text-white">
                            Rp {{ number_format(($bonusTotal ?? 0) - ($withdrawnTotal ?? 0), 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                </div>

                <div class="mt-6 flex divide-x divide-neutral-200 border-t border-neutral-100 pt-4 dark:divide-neutral-700 dark:border-neutral-800">
                    <div class="flex-1 pr-4">
                        <p class="text-xs text-neutral-500">Total Pendapatan</p>
                        <p class="font-semibold text-green-600">
                            + Rp {{ number_format($bonusTotal ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex-1 pl-4">
                        <p class="text-xs text-neutral-500">Total Dicairkan</p>
                        <p class="font-semibold text-red-500">
                            - Rp {{ number_format($withdrawnTotal ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('dashboard.withdrawals') }}" class="flex w-full items-center justify-center rounded-lg bg-neutral-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-neutral-800 dark:bg-white dark:text-neutral-900 dark:hover:bg-gray-200">
                    Kelola Penarikan
                </a>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- CARD 3: NETWORK STRUCTURE --}}
        {{-- ========================================== --}}
        <div class="relative flex flex-col justify-between overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div>
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-indigo-50 p-2 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="10" y="3" width="4" height="4" rx="1"/><rect x="3" y="17" width="4" height="4" rx="1"/><rect x="17" y="17" width="4" height="4" rx="1"/><path d="M12 7v5"/><path d="M12 12H5v5"/><path d="M12 12h7v5"/></svg>
                        </div>
                        <h3 class="font-semibold text-neutral-900 dark:text-white">Struktur Jaringan</h3>
                    </div>
                    <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">Max 5 Gen</span>
                </div>

                <div class="space-y-4">
                    @foreach($networkStats as $level => $count)
                        <div>
                            <div class="mb-1 flex items-center justify-between">
                                <span class="text-sm font-medium text-neutral-600 dark:text-neutral-300">
                                    Generasi {{ $level }}
                                    @if($level == 1) <span class="ml-1 text-xs text-neutral-400">(Direct)</span> @endif
                                </span>
                                <span class="text-sm font-bold text-neutral-900 dark:text-white">{{ $count }} Member</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800">
                                @php
                                    $percentage = $totalMembers > 0 ? ($count / $totalMembers) * 100 : 0;
                                    $colors = [1 => 'bg-blue-500', 2 => 'bg-purple-500', 3 => 'bg-pink-500', 4 => 'bg-orange-500', 5 => 'bg-green-500'];
                                @endphp
                                <div class="{{ $colors[$level] ?? 'bg-gray-500' }} h-full rounded-full transition-all duration-500" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between border-t border-neutral-100 pt-4 dark:border-neutral-800">
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-bold text-neutral-900 dark:text-white">{{ $totalMembers }}</span>
                    <span class="text-xs text-neutral-500">Total Member</span>
                </div>
                <a href="{{ route('dashboard.members') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                    Lihat Detail &rarr;
                </a>
            </div>
        </div>
        
    </div>

    {{-- 3. TRANSACTION HISTORY TABLE --}}
    <div>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Riwayat Transaksi</h2>
        </div>

        <div class="relative overflow-x-auto rounded-lg border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900 mb-4">
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th>No</x-table.th>
                        <x-table.th>Sumber (Downline)</x-table.th>
                        <x-table.th>Toko / UMKM</x-table.th>
                        <x-table.th>Kode Transaksi</x-table.th>
                        <x-table.th>Tanggal</x-table.th>
                        <x-table.th>Level</x-table.th>
                        <x-table.th>Persen</x-table.th>
                        <x-table.th class="text-right">Bonus/Tarikan (Rp)</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @forelse ($transactions as $item)
                        <x-table.tr>
                            <x-table.td>{{ $transactions->firstItem() + $loop->index }}</x-table.td>
                            
                            {{-- CEK APAKAH INI BONUS ATAU WITHDRAWAL --}}
                            @if($item->log_type === 'bonus')

                                {{-- TAMPILAN UNTUK BONUS --}}
                                <x-table.td>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        @if(in_array($item->LevelMember, ['Leader', 'Level 1']))
                                            Diri Sendiri
                                        @else
                                            {{ $item->sourceMember->user->name ?? '-' }}
                                        @endif
                                    </span>
                                    @if(isset($item->sourceMember->member_code))
                                        <div class="text-xs text-gray-500">{{ $item->sourceMember->member_code }}</div>
                                    @endif
                                </x-table.td>
                                <x-table.td>{{ $item->business->name ?? '-' }}</x-table.td>
                                <x-table.td>
                                    <span class="font-mono text-xs text-gray-500">
                                        {{ $item->transaction_code ?? '-' }}
                                    </span>
                                </x-table.td>

                                <x-table.td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}</x-table.td>

                                <x-table.td>
                                    @php
                                        $lvl = $item->LevelMember ?? '-';
                                        $color = match($lvl) {
                                            'Leader' => 'bg-purple-50 text-purple-700 ring-purple-700/10',
                                            'Level 1' => 'bg-green-50 text-green-700 ring-green-600/20',
                                            '-' => 'bg-gray-50 text-gray-600 ring-gray-500/10',
                                            default => 'bg-blue-50 text-blue-700 ring-blue-700/10',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $color }}">
                                        {{ $lvl }}
                                    </span>
                                </x-table.td>

                                <x-table.td>{{ $item->BonusPercent ?? 0 }}%</x-table.td>
                                
                                <x-table.td class="text-right font-bold text-green-600">
                                    + Rp {{ number_format($item->bonus ?? 0, 0, ',', '.') }}
                                </x-table.td>

                            @else

                                {{-- TAMPILAN UNTUK WITHDRAWAL (PENARIKAN) --}}
                                <x-table.td>
                                    <span class="text-blue-600 font-semibold">Penarikan Saldo</span>
                                </x-table.td>
                                <x-table.td>-</x-table.td>
                                <x-table.td>-</x-table.td>

                                <x-table.td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}</x-table.td>

                                <x-table.td>
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">Withdrawal</span>
                                </x-table.td>
                                
                                <x-table.td>-</x-table.td>

                                <x-table.td class="text-red-500 font-bold text-right">
                                    - Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </x-table.td>

                            @endif

                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="8" class="text-center py-8 text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <p>Belum ada riwayat transaksi.</p>
                                </div>
                            </x-table.td>
                        </x-table.tr>
                    @endforelse
                </tbody>

                {{-- FOOTER UNTUK TOTAL KESELURUHAN --}}
                <tfoot class="bg-gray-50 dark:bg-neutral-800 border-t border-neutral-200 dark:border-neutral-700">
                    <x-table.tr>
                        <x-table.td colspan="7" class="text-right font-bold text-gray-900 dark:text-white py-4">
                            Sisa Saldo Aktif:
                        </x-table.td>
                        <x-table.td class="text-right font-bold text-blue-600 text-lg">
                            Rp {{ number_format($currentBalance ?? 0, 0, ',', '.') }}
                        </x-table.td>
                    </x-table.tr>
                </tfoot>

            </x-table.table>
            
            @if ($transactions->hasPages())
                <div class="border-t border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-900">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Panggil Library dari CDN --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html-to-image/1.11.11/html-to-image.min.js"></script>

<script>
    // FUNGSI 1: Render QR Code (Biarkan sama seperti sebelumnya)
    function renderQRCode() {
        const qrElement = document.getElementById('qrcode');
        if (qrElement && typeof QRious !== 'undefined') {
            new QRious({
                element: qrElement,
                value: '{{ auth()->user()->member->member_code ?? "UNKNOWN" }}',
                size: 250, 
                background: 'white',
                foreground: 'black',
                level: 'H'
            });
        }
    }
    document.addEventListener("DOMContentLoaded", renderQRCode);
    document.addEventListener("livewire:navigated", renderQRCode); 
    setTimeout(renderQRCode, 500); 

    // FUNGSI 2: Cetak (Print) Kartu Langsung
    function printCard() {
        const cardElement = document.getElementById('member-card-element');
        
        setTimeout(() => {
            htmlToImage.toPng(cardElement, {
                pixelRatio: 3, // Kualitas HD untuk printer
                style: { transform: 'none' },
                filter: function (node) {
                    return node.tagName !== 'SCRIPT';
                }
            })
            .then(function (dataUrl) {
                // Membuka tab baru (tersembunyi/popup) khusus untuk print
                const printWindow = window.open('', '_blank');
                
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Cetak Kartu Member</title>
                            <style>
                                body {
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                    height: 100vh;
                                    margin: 0;
                                    background-color: white;
                                }
                                img {
                                    width: 8.5cm; /* Ukuran standar id card / KTP */
                                    height: auto;
                                    border-radius: 10px;
                                }
                                @media print {
                                    @page { margin: 0; size: auto; }
                                    body { margin: 1cm; align-items: flex-start; }
                                }
                            </style>
                        </head>
                        <body>
                            <img src="${dataUrl}" onload="window.print(); window.close();" />
                        </body>
                    </html>
                `);
                printWindow.document.close();
            })
            .catch(function (error) {
                console.error('Oops, gagal menyiapkan kartu untuk dicetak!', error);
                alert('Gagal mencetak kartu. Silakan coba lagi.');
            });
        }, 100); 
    }
</script>