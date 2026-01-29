<div>
    {{-- header --}}
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Transactions" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-blue-600 sm:text-2xl dark:text-white">Total Transactions: Rp. {{ number_format($transactionTotal ?? 0) }}</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search"
                        placeholder="Search Transactions" />
                </div>
            </div>
        </div>
    </div>

    {{-- @dd($members) --}}

    {{-- table --}}
    <div class="relative overflow-x-auto rounded-lg border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <x-table.table>
            <x-table.thead>
                <x-table.tr>
                    <x-table.th>No</x-table.th>
                    <x-table.th>Asal Transaksi</x-table.th> {{-- Siapa yang beli? --}}
                    <x-table.th>Toko / UMKM</x-table.th>
                    <x-table.th>Kode & Tanggal</x-table.th>
                    <x-table.th>Level</x-table.th>
                    <x-table.th>Persentase</x-table.th>
                    <x-table.th class="text-right">Bonus Masuk</x-table.th>
                </x-table.tr>
            </x-table.thead>

            <tbody>
                @forelse ($transactions as $item)   
                    <x-table.tr>
                        <x-table.td>{{ $transactions->firstItem() + $loop->index }}</x-table.td>
                        
                        {{-- KOLOM ASAL TRANSAKSI --}}
                        <x-table.td>
                            <div class="flex flex-col">
                                {{-- Cek apakah Source Member == Member yang Login? --}}
                                @if($item->source_member_id == auth()->user()->member->id)
                                    <span class="font-bold text-gray-900 dark:text-white">Pribadi (Saya)</span>
                                    <span class="text-xs text-gray-500">Cashback Belanja</span>
                                @else
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ optional($item->sourceMember->user)->name ?? 'Member Terhapus' }}
                                    </span>
                                    <span class="text-xs text-indigo-500">
                                        Kode: {{ optional($item->sourceMember)->member_code }}
                                    </span>
                                @endif
                            </div>
                        </x-table.td>

                        <x-table.td>{{ optional($item->transaction->business)->name ?? '-' }}</x-table.td>
                        
                        <x-table.td>
                            <div class="flex flex-col">
                                <span class="font-mono text-xs font-medium text-gray-700 dark:text-gray-300">
                                    {{ optional($item->transaction)->transaction_code }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}
                                </span>
                            </div>
                        </x-table.td>
                        
                        {{-- BADGE LEVEL --}}
                        <x-table.td>
                            @if($item->level == 0)
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    Cashback
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                    Gen {{ $item->level }}
                                </span>
                            @endif
                        </x-table.td>

                        <x-table.td>
                            <span class="text-gray-600">{{ $item->percentage }}%</span>
                        </x-table.td>
                        
                        {{-- JUMLAH UANG --}}
                        <x-table.td class="text-right">
                            <span class="font-bold text-green-600">
                                + Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </span>
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <x-table.tr>
                        <x-table.td colspan="7" class="py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <p>Belum ada riwayat bonus atau transaksi.</p>
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
