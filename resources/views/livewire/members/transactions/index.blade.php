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
                    <dt class="truncate text-sm font-medium text-blue-500">Sisa Saldo Bonus</dt>
                    <dd class="mt-1 text-2xl font-bold tracking-tight text-blue-700">
                        Rp {{ number_format(($totalIncome ?? 0) - ($totalWithdrawal ?? 0), 0, ',', '.') }}
                    </dd>
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
                    <x-table.th class="text-right">Bonus Kamu</x-table.th>
                </x-table.tr>
            </x-table.thead>

            <tbody>
                @forelse ($transactions as $item)
                    <x-table.tr>
                        <x-table.td>{{ $transactions->firstItem() + $loop->index }}</x-table.td>
                        
                        {{-- CEK APAKAH INI BONUS ATAU WITHDRAWAL --}}
                        @if($item->log_type === 'bonus')

                            {{-- TAMPILAN UNTUK BONUS --}}
                            <x-table.td>{{ $item->sourceMember->user->name ?? '-' }}</x-table.td>
                            <x-table.td>{{ $item->business->name ?? '-' }}</x-table.td>
                            <x-table.td>{{ $item->transaction_code }}</x-table.td>
                            
                            {{-- PERBAIKAN TANGGAL --}}
                            <x-table.td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</x-table.td>
                            
                            <x-table.td>{{ $item->LevelMember }}</x-table.td>
                            
                            {{-- TAMBAHAN KOLOM PERSENTASE (KARENA DI HEADER ADA) --}}
                            <x-table.td>{{ $item->BonusPercent ?? 0 }}%</x-table.td>

                            <x-table.td class="text-green-600 font-bold text-right">
                                + Rp {{ number_format($item->bonus, 0, ',', '.') }}
                            </x-table.td>

                        @else

                            {{-- TAMPILAN UNTUK WITHDRAWAL (PENARIKAN) --}}
                            <x-table.td>
                                <span class="text-red-600 font-semibold">Penarikan Saldo</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="text-red-600 font-semibold">-</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="text-red-600 font-semibold">-</span>
                            </x-table.td>
                            
                            {{-- PERBAIKAN TANGGAL --}}
                            <x-table.td>
                                <span class="text-red-600 font-semibold">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</span>
                            </x-table.td>
                            
                            <x-table.td>
                                <span class="bg-gray-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Withdrawal</span>
                            </x-table.td>
                            
                            {{-- KOSONGKAN KOLOM PERSENTASE UNTUK WITHDRAWAL --}}
                            <x-table.td>
                                <span class="bg-gray-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">-</span>
                            </x-table.td>

                            <x-table.td class="text-red-500 font-bold text-right">
                                - Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </x-table.td>

                        @endif

                    </x-table.tr>
                @empty
                    <x-table.tr>
                        <x-table.td colspan="8" class="text-center py-4 text-gray-500">
                            Belum ada transaksi maupun penarikan.
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