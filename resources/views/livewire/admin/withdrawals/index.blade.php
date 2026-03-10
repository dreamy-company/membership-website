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
                                {{-- Wrapper flex agar tombol sejajar ke samping (horizontal) --}}
                                <div class="flex items-center justify-center gap-2">
                                    
                                    {{-- Tombol Preview PDF --}}
                                    @if($item->payment_receipt)
                                        <a href="{{ asset('storage/' . $item->payment_receipt) }}" target="_blank" 
                                        {{-- Tambahkan inline-flex dan shrink-0 agar bentuknya kotak sempurna dan tidak menciut --}}
                                        class="inline-flex shrink-0 items-center justify-center p-2 text-blue-600 bg-white hover:bg-blue-50 rounded-lg transition-colors shadow-sm border border-blue-200" 
                                        title="Preview PDF">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10V4a1 1 0 0 0-1-1H9.914a1 1 0 0 0-.707.293L5.293 7.207A1 1 0 0 0 5 7.914V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2M10 3v4a1 1 0 0 1-1 1H5m5 6h9m0 0-2-2m2 2-2 2"/>
                                            </svg>
                                        </a>
                                    @endif

                                    {{-- Tombol Delete (Atau tombol bawaan lainnya) --}}
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
                <div class="p-4">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>
    </div>

     <!-- Modal -->
    @if($isOpen)
        <x-modal.form-modal :formTitle="$withdrawal_id ? 'Edit Withdrawal' : 'Add Withdrawal'" action="store()">
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th>No</x-table.th>
                        <x-table.th>Nama</x-table.th>
                        <x-table.th>Bonus</x-table.th>
                        <x-table.th>Pilih</x-table.th> {{-- Ganti Judul Kolom --}}
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @forelse ($memberBalance as $item)
                        <x-table.tr>
                            {{-- Gunakan Skenario 1 atau 2 dari jawaban sebelumnya (Ini contoh pakai pagination) --}}
                            <x-table.td>{{ $loop->iteration }}</x-table.td>
                            <x-table.td>{{ $item->user->name }}</x-table.td>
                            <x-table.td>Rp {{ number_format($item->transactions_sum_bonus, 0, ',', '.') }}</x-table.td>
                            <x-table.td>
                                
                                {{-- BERUBAH MENJADI CHECKBOX --}}
                                <input type="checkbox" 
                                    wire:model="selectedMembers" 
                                    value="{{ $item->id }}" 
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">

                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="4" class="text-center py-4 text-gray-500">
                                Tidak ada member yang memiliki bonus.
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
