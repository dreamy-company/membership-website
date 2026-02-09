<div>
    {{-- HEADER & BREADCRUMBS --}}
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                {{-- Tombol Kembali --}}
                <a href="{{ route('admin.members') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Members
                </a>
                
                <h1 class="text-xl font-bold text-gray-900 sm:text-2xl dark:text-white">
                    Detail Transaksi: <span class="text-blue-600">{{ $member->user->name }}</span>
                </h1>
                <p class="text-sm text-gray-500">Kode Member: {{ $member->member_code }}</p>
            </div>

            {{-- STATISTIK KEUANGAN MEMBER --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                {{-- Card 1: Total Bonus --}}
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <dt class="text-sm font-medium text-green-600 truncate">Total Bonus Masuk</dt>
                    <dd class="mt-1 text-2xl font-bold text-green-700">Rp {{ number_format($totalBonus, 0, ',', '.') }}</dd>
                </div>

                {{-- Card 2: Total Penarikan --}}
                <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
                    <dt class="text-sm font-medium text-orange-600 truncate">Total Penarikan (Withdraw)</dt>
                    <dd class="mt-1 text-2xl font-bold text-orange-700">Rp {{ number_format($totalWithdrawn, 0, ',', '.') }}</dd>
                </div>

                {{-- Card 3: Sisa Saldo --}}
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <dt class="text-sm font-medium text-blue-600 truncate">Sisa Saldo Saat Ini</dt>
                    <dd class="mt-1 text-2xl font-bold text-blue-700">Rp {{ number_format($currentBalance, 0, ',', '.') }}</dd>
                </div>
            </div>

            {{-- SEARCH BAR --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center mb-4 sm:mb-0 w-full sm:w-1/3">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search" placeholder="Cari Transaksi..." class="w-full" />
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE DATA --}}
    <div class="relative overflow-x-auto rounded-lg border border-neutral-200 bg-white shadow-sm mt-4 mx-4 mb-4">
        <x-table.table>
            <x-table.thead>
                <x-table.tr>
                    <x-table.th>No</x-table.th>
                    <x-table.th>Sumber (Downline)</x-table.th>
                    <x-table.th>Toko</x-table.th>
                    <x-table.th>Kode & Tanggal</x-table.th>
                    <x-table.th>Level</x-table.th>
                    <x-table.th>Persen</x-table.th>
                    <x-table.th class="text-right">Bonus Diterima</x-table.th>
                </x-table.tr>
            </x-table.thead>

            <tbody>
                @forelse ($transactions as $item)   
                    <x-table.tr>
                        <x-table.td>{{ $transactions->firstItem() + $loop->index }}</x-table.td>
                        
                        {{-- Sumber Bonus --}}
                        <x-table.td>
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900">
                                    @if(in_array($item->LevelMember, ['Leader', 'Level 1']))
                                        <span class="text-blue-600 font-semibold">Pribadi</span>
                                    @else
                                        {{ $item->sourceMember->user->name ?? 'Unknown' }}
                                    @endif
                                </span>
                                <span class="text-xs text-gray-500">{{ $item->sourceMember->member_code ?? '-' }}</span>
                            </div>
                        </x-table.td>

                        <x-table.td>{{ $item->business->name ?? '-' }}</x-table.td>
                        
                        <x-table.td>
                            <div class="flex flex-col">
                                <span class="font-mono text-xs font-bold">{{ $item->transaction_code }}</span>
                                <span class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}
                                </span>
                            </div>
                        </x-table.td>
                        
                        {{-- Level Badge --}}
                        <x-table.td>
                            @php
                                $color = match($item->LevelMember) {
                                    'Leader' => 'bg-purple-100 text-purple-700',
                                    'Level 1' => 'bg-green-100 text-green-700',
                                    default => 'bg-blue-100 text-blue-700',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $color }}">
                                {{ $item->LevelMember }}
                            </span>
                        </x-table.td>

                        <x-table.td>{{ $item->BonusPercent }}%</x-table.td>
                        
                        {{-- Bonus Amount --}}
                        <x-table.td class="text-right font-bold text-green-600">
                            + {{ number_format($item->bonus, 0, ',', '.') }}
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <x-table.tr>
                        <x-table.td colspan="7" class="py-8 text-center text-gray-500">
                            Belum ada riwayat transaksi untuk member ini.
                        </x-table.td>
                    </x-table.tr>
                @endforelse
            </tbody>
        </x-table.table>
        
        @if ($transactions->hasPages())
            <div class="border-t border-neutral-200 bg-gray-50 p-4">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>