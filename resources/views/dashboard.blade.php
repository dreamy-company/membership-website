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

    {{-- 2. STATS & NETWORK GRID --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
        
        {{-- CARD 1: BONUS WALLET --}}
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

        {{-- CARD 2: NETWORK STRUCTURE --}}
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

                {{-- Network List --}}
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
                            
                            {{-- Visual Bar --}}
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

            {{-- Network Footer --}}
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
            {{-- Optional: Add View All button here --}}
        </div>

        <div class="relative overflow-x-auto rounded-lg border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th>No</x-table.th>
                        <x-table.th>Member Asal</x-table.th>
                        <x-table.th>UMKM / Toko</x-table.th>
                        <x-table.th>Kode Transaksi</x-table.th>
                        <x-table.th>Tanggal</x-table.th>
                        <x-table.th>Jam</x-table.th>
                        <x-table.th>Level</x-table.th>
                        <x-table.th>Persentase</x-table.th>
                        <x-table.th class="text-right">Bonus (Rp)</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @forelse ($transactions as $item)   
                        <x-table.tr>
                            <x-table.td>{{ $transactions->firstItem() + $loop->index }}</x-table.td>
                            
                            {{-- Member Info --}}
                            <x-table.td>
                                @if($item->level == 0)
                                    <span class="font-medium text-gray-900 dark:text-white">Diri Sendiri</span>
                                @else
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ optional($item->sourceMember->user)->name ?? 'Unknown' }}
                                    </span>
                                @endif
                            </x-table.td>

                            <x-table.td>{{ optional($item->transaction->business)->name ?? '-' }}</x-table.td>
                            
                            <x-table.td>
                                <span class="font-mono text-xs text-gray-500">
                                    {{ $item->transaction->transaction_code }}
                                </span>
                            </x-table.td>

                            <x-table.td>
                                {{ \Carbon\Carbon::parse($item->transaction->transaction_date)->format('d M Y') }}
                            </x-table.td>

                            <x-table.td>
                                {{ $item->transaction->created_at->format('H:i') }}
                            </x-table.td>
                            
                            {{-- Logic Level Badge --}}
                            <x-table.td>
                                @if($item->level == 0)
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                        Cashback
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                        Gen {{ $item->level }}
                                    </span>
                                @endif
                            </x-table.td>

                            <x-table.td>{{ $item->percentage }}%</x-table.td>
                            
                            <x-table.td class="text-right font-bold text-green-600">
                                + {{ number_format($item->amount, 0, ',', '.') }}
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            {{-- FIX: Colspan harus sesuai jumlah kolom (9 kolom) --}}
                            <x-table.td colspan="9" class="py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <p>Belum ada riwayat transaksi.</p>
                                </div>
                            </x-table.td>
                        </x-table.tr>
                    @endforelse
                </tbody>
            </x-table.table>
            
            @if ($transactions->hasPages())
                <div class="border-t border-neutral-200 bg-gray-50 p-4 dark:border-neutral-700 dark:bg-neutral-800">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>