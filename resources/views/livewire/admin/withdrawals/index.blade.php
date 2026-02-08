<div>
    {{-- header --}}
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Withdrawals" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search" placeholder="Search Withdrawals" />
                </div>
                <div>
                    <x-widget.button color="neutral" name="Add Withdrawal" action="openModal()" />
                </div>
            </div>
        </div>
    </div>

    {{-- @dd($provinces) --}}

    {{-- table --}}
    <div class="w-full mt-6 px-4 pb-4">
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-md border border-default">
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th>No</x-table.th>
                        <x-table.th>Member</x-table.th>
                        <x-table.th>Withdrawal Amount</x-table.th>
                        <x-table.th>Date</x-table.th>
                        <x-table.th>Actions</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @forelse ($withdrawals as $item)
                        <x-table.tr>
                            <x-table.td>{{ $withdrawals->firstItem() + $loop->index }}</x-table.td>
                            <x-table.td>{{ $item->member->user->name }}</x-table.td>
                            <x-table.td>{{ number_format($item->amount) }}</x-table.td>
                            <x-table.td>{{ $item->date }}</x-table.td>
                            <x-table.td>
                                <x-widget.button-icon type="edit" action="openModal({{ $item->id }})" />
                                <x-widget.button-icon type="delete" action="confirmDelete({{ $item->id }})" />
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="5" class="text-center py-4">
                                No withdrawals found.
                            </x-table.td>
                        </x-table.tr>
                    @endforelse
                </tbody>
            </x-table.table>
           @if ($withdrawals->hasPages())
                <div class="p-4">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>
    </div>

     <!-- Modal -->
    @if($isOpen)
        <x-modal.form-modal :formTitle="$withdrawal_id ? 'Edit Withdrawal' : 'Add Withdrawal'" action="store()">
            <div class="py-4 px-2 md:py-6">
                <div class="grid grid-cols-2 gap-4 mb-4"> {{-- gap-4 biar lebih lega --}}
                    
                    {{-- Di Modal Withdrawal --}}
                    <div class="gap-2">
                        <x-modal.searchable-select 
                            wire:model.live="member_id"  {{-- .live di sini boleh dihapus, krn sudah dihandle script --}}
                            name="member_id" 
                            label="Member" 
                            :options="$members->map(fn($m) => ['value' => $m->id, 'label' => $m->user->name])" 
                            placeholder="Cari Member..."
                            
                            :liveUpdates="true"  {{-- <--- INI KUNCINYA --}}
                        />
                    </div>

                    {{-- 2. SALDO TERSEDIA (READONLY) --}}
                    <div class="gap-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Sisa Bonus Tersedia
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <span class="text-gray-500 text-sm font-bold">Rp</span>
                            </div>
                            <input 
                                type="text" 
                                value="{{ number_format($available_balance, 0, ',', '.') }}" 
                                disabled
                                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 cursor-not-allowed font-bold" 
                                placeholder="0"
                            >
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Total Bonus (Ledger) - Total Withdrawal
                        </p>
                    </div>       
                </div>

                {{-- Input Lainnya Tetap --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="gap-2">
                        <x-modal.input name="amount" label="Jumlah Penarikan" type="number" placeholder="Contoh: 50000" />
                        {{-- Validasi visual jika narik lebih dari saldo --}}
                        @if($amount > $available_balance)
                            <span class="text-xs text-red-500 font-bold">Saldo tidak cukup!</span>
                        @endif
                    </div>        
                    <div class="gap-2">
                        <x-modal.input name="date" label="Tanggal Request" type="date" />
                    </div>        
                </div>

                {{-- Upload Receipt Tetap --}}
                <div class="grid grid-cols-1 gap-2 mb-4">
                {{-- ... kode upload file kamu yg lama ... --}}

                <x-modal.input-file model="payment_receipt" name="payment_receipt" label="Bukti Transfer" />
                </div>
            </div>
        </x-modal.form-modal>
    @endif
   
    <x-alerts.success/>
    <x-alerts.delete-confirmation/>


</div>
