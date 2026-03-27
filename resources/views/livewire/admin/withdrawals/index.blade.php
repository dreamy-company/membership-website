<div>
    {{-- header --}}
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
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

    {{-- Filter Section --}}
    <div class="w-full mt-4 px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-white shadow-xs rounded-md border border-gray-200">
            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700">Tanggal Mulai</label>
                <input type="date" wire:model.live="start_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700">Tanggal Akhir</label>
                <input type="date" wire:model.live="end_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            {{-- Tombol Reset Filter --}}
            <div class="flex items-end">
                <button type="button" wire:click="$set('start_date', null); $set('end_date', null)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

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
                                <div class="flex items-center justify-center gap-2">
                                    @if($item->payment_receipt)
                                        <a href="{{ asset('storage/' . $item->payment_receipt) }}" target="_blank" 
                                        class="inline-flex shrink-0 items-center justify-center p-2 text-blue-600 bg-white hover:bg-blue-50 rounded-lg transition-colors shadow-sm border border-blue-200" 
                                        title="Preview PDF">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10V4a1 1 0 0 0-1-1H9.914a1 1 0 0 0-.707.293L5.293 7.207A1 1 0 0 0 5 7.914V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2M10 3v4a1 1 0 0 1-1 1H5m5 6h9m0 0-2-2m2 2-2 2"/>
                                            </svg>
                                        </a>
                                    @endif
                                    <x-widget.button-icon type="delete" action="confirmDelete({{ $item->id }})" />
                                </div>
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
                <div class="p-4 bg-white border-t border-gray-200">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>
    </div>

     @if($isOpen)
        <x-modal.form-modal :formTitle="$withdrawal_id ? 'Edit Withdrawal' : 'Add Withdrawal'" action="store()" width='max-w-[60%] w-full'>
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th>No</x-table.th>
                        <x-table.th>Nama</x-table.th>
                        <x-table.th>Total Bonus</x-table.th>
                        <x-table.th>Sudah Ditarik</x-table.th>
                        <x-table.th>Sisa Bonus</x-table.th>
                        <x-table.th width="20%">Jumlah Penarikan</x-table.th>
                        <x-table.th>Pilih</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @php $nomor = 1; @endphp 
                    
                    @forelse ($memberBalance as $item)
                        @if($item['sisa_saldo'] > 0)
                            <x-table.tr>
                                <x-table.td>{{ $nomor++ }}</x-table.td> 
                                <x-table.td>{{ $item['name'] }}</x-table.td>
                                <x-table.td>Rp {{ number_format($item['total_bonus'], 0, ',', '.') }}</x-table.td>
                                <x-table.td class="text-red-500">Rp {{ number_format($item['total_ditarik'], 0, ',', '.') }}</x-table.td>
                                <x-table.td class="font-bold text-blue-600">Rp {{ number_format($item['sisa_saldo'], 0, ',', '.') }}</x-table.td>
                                
                                <x-table.td>
                                    <div class="relative w-full">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <span class="text-slate-400 text-sm font-medium">Rp</span>
                                        </div>
                                        <input type="text" 
                                            value="{{ in_array($item['id'], $selectedMembers) ? number_format($item['sisa_saldo'], 0, ',', '.') : 0 }}" 
                                            class="block w-full pl-9 pr-3 py-2 text-sm font-semibold text-right transition-colors duration-200 rounded-lg border border-slate-200 shadow-sm bg-slate-50 text-slate-700 cursor-not-allowed focus:ring-0 focus:border-slate-200"
                                            disabled>
                                    </div>
                                </x-table.td>

                                <x-table.td>
                                    <input type="checkbox" 
                                        wire:model.live="selectedMembers" 
                                        value="{{ $item['id'] }}" 
                                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                                </x-table.td>
                            </x-table.tr>
                        @endif
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="7" class="text-center py-4 text-gray-500">
                                Tidak ada member yang memiliki saldo.
                            </x-table.td>
                        </x-table.tr>
                    @endforelse
                </tbody>
            </x-table.table>
        </x-modal.form-modal>
    @endif
   
    <x-alerts.success/>
    <x-alerts.delete-confirmation/>
</div>