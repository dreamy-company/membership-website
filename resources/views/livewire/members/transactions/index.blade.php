<div>
    {{-- Header & Statistik Singkat --}}
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Transactions" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            
            {{-- Statistik Ringkas (Income & Omzet) --}}
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-lg border border-blue-100 bg-blue-50 p-4">
                    <dt class="truncate text-sm font-medium text-blue-500">Total Bonus Diterima (Income)</dt>
                    <dd class="mt-1 text-2xl font-bold tracking-tight text-blue-700">Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}</dd>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white p-4">
                    <dt class="truncate text-sm font-medium text-gray-500">Total Omzet Jaringan</dt>
                    <dd class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Rp {{ number_format($totalOmzet ?? 0, 0, ',', '.') }}</dd>
                </div>
            </div>

            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0 w-full sm:w-1/3">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search" placeholder="Cari Transaksi / Downline..." class="w-full" />
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="relative overflow-x-auto rounded-lg border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900 mt-4 mx-4 mb-4">
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
                    <x-table.th class="text-right">Nilai Belanja</x-table.th>
                    <x-table.th class="text-right">Bonus Kamu</x-table.th>
                </x-table.tr>
            </x-table.thead>

            <tbody>
                @forelse ($transactions as $item)   
                    <x-table.tr>
                        <x-table.td>{{ $transactions->firstItem() + $loop->index }}</x-table.td>
                        
                        {{-- 1. SUMBER BONUS --}}
                        <x-table.td>
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{-- Logic: Jika Level Leader/Level 1 = Diri Sendiri --}}
                                    @if(in_array($item->LevelMember, ['Leader', 'Level 1']))
                                        <span class="text-blue-600 font-semibold">Pribadi (Saya)</span>
                                    @else
                                        {{ $item->sourceMember->user->name ?? 'Member Terhapus' }}
                                    @endif
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $item->sourceMember->member_code ?? '-' }}
                                </span>
                            </div>
                        </x-table.td>

                        {{-- 2. NAMA TOKO --}}
                        <x-table.td>{{ $item->business->name ?? '-' }}</x-table.td>
                        
                        {{-- 3. KODE TRX --}}
                        <x-table.td>
                            <span class="font-mono text-xs text-gray-500">
                                {{ $item->transaction_code }}
                            </span>
                        </x-table.td>

                        {{-- 4. TANGGAL --}}
                        <x-table.td>
                            <div class="flex flex-col">
                                <span>{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</span>
                                <span class="text-xs text-gray-400">{{ $item->created_at ? $item->created_at->format('H:i') : '' }}</span>
                            </div>
                        </x-table.td>
                        
                        {{-- 5. LEVEL BADGE --}}
                        <x-table.td>
                            @php
                                $lvl = $item->LevelMember ?? '-';
                                $color = match($lvl) {
                                    'Leader' => 'bg-purple-50 text-purple-700 ring-purple-700/10',
                                    'Level 1' => 'bg-green-50 text-green-700 ring-green-600/20',
                                    '-' => 'bg-gray-50 text-gray-600 ring-gray-500/10',
                                    default => 'bg-blue-50 text-blue-700 ring-blue-700/10', // Level 2 dst
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $color }}">
                                {{ $lvl }}
                            </span>
                        </x-table.td>

                        {{-- 6. PERSENTASE --}}
                        <x-table.td>
                            <span class="text-gray-600">{{ $item->BonusPercent }}%</span>
                        </x-table.td>
                        
                        {{-- 7. NILAI BELANJA (AMOUNT) --}}
                        <x-table.td class="text-right text-gray-500">
                             {{ number_format($item->amount, 0, ',', '.') }}
                        </x-table.td>

                        {{-- 8. BONUS DITERIMA (INCOME) --}}
                        <x-table.td class="text-right font-bold text-green-600">
                            + {{ number_format($item->bonus, 0, ',', '.') }}
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <x-table.tr>
                        <x-table.td colspan="9" class="py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Belum ada riwayat transaksi bonus.</p>
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

    <x-alerts.success />
    <x-alerts.delete-confirmation />
</div>