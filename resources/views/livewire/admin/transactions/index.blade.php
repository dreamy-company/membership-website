<div>
    {{-- Header --}}
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Transactions" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search" placeholder="Search Transactions" />
                </div>
                <div>
                    <x-widget.button color="neutral" name="Add Transaction" action="openModal()" />
                    {{-- <x-widget.button color="neutral" name="Import Transactions" action="openImportModal()" /> --}}
                    <x-widget.button color="neutral" name="Activity Log" action="redirectToActivityLog()" />
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="w-full mt-4 px-4">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 p-4 bg-white shadow-xs rounded-md border border-gray-200">
            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700">Periode Mulai</label>
                <input type="date" wire:model.live="start_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700">Periode Akhir</label>
                <input type="date" wire:model.live="end_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700">UMKM</label>
                <select wire:model.live="filter_umkm" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">Semua UMKM</option>
                    @foreach($businesses as $business)
                        <option value="{{ $business->id }}">{{ $business->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700">Member Code</label>
                <input type="text" wire:model.live.debounce.300ms="filter_member_code" placeholder="Ketik kode member..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="w-full mt-6 px-4 pb-4">
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-md border border-default">
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th>No</x-table.th>
                        <x-table.th class="whitespace-nowrap">UMKM</x-table.th>
                        <x-table.th>Member</x-table.th>
                        <x-table.th>No Nota / Bill</x-table.th>
                        <x-table.th>Tanggal Nota</x-table.th>
                        <x-table.th>Jumlah Nota</x-table.th>
                        <x-table.th>Bonus Dibagi</x-table.th>
                        <x-table.th>Level</x-table.th>
                        <x-table.th>Bonus Percent</x-table.th>
                        <x-table.th>Bonus</x-table.th>
                        <x-table.th>Actions</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @forelse ($transactions as $item)
                        <x-table.tr>
                            <x-table.td>{{ $transactions->firstItem() + $loop->index }}</x-table.td>
                            <x-table.td class="whitespace-nowrap">{{ $item->business->name ?? '-' }}</x-table.td>
                            <x-table.td>{{ $item->member->user->name ?? '-' }} ({{ $item->member->member_code ?? '-' }})</x-table.td>
                            <x-table.td>{{ $item->transaction_code }}</x-table.td>
                            <x-table.td>{{ $item->transaction_date }}</x-table.td>
                            <x-table.td>{{ number_format($item->amount ?? 0) }}</x-table.td>
                            <x-table.td>{{ number_format($item->balance ?? 0) }}</x-table.td>
                            <x-table.td>{{ $item->LevelMember ?? '-' }}</x-table.td>
                            <x-table.td>{{ number_format($item->BonusPercent ?? 0) }}%</x-table.td>
                            <x-table.td>{{ number_format($item->bonus ?? 0) }}</x-table.td>
                            <x-table.td>
                                <div class="flex gap-2">
                                    <x-widget.button-icon type="edit" action="openModal({{ $item->id }})" />
                                    {{-- <x-widget.button-icon type="delete" action="confirmDelete({{ $item->id }})" /> --}}
                                </div>
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="11" class="text-center py-4">No Transactions found.</x-table.td>
                        </x-table.tr>
                    @endforelse
                </tbody>
            </x-table.table>
            
            @if ($transactions->hasPages())
                <div class="p-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Form Modal --}}
    @if($isOpen)
        <x-modal.form-modal :formTitle="$record_id ? 'Detail Transaction' : 'Add Transaction'" action="store()" height="h-auto">
            <div class="grid gap-4 grid-cols-1 py-4 md:py-6">
                
                @php
                    $isEdit = !!$record_id;

                    $businessOptions = $businesses->map(fn($b) => [
                        'value' => $b->id, 
                        'label' => $b->name
                    ])->toArray();

                    $memberOptions = $members->map(fn($m) => [
                        'value' => $m->id, 
                        'label' => $m->user->name . ' (' . ($m->member_code ?? '-') . ')' 
                    ])->toArray();
                @endphp

                <div class="grid grid-cols-2 gap-2">
                    <div class="mb-2">
                        <x-modal.searchable-select 
                            wire:model="business_id" 
                            name="business_id" 
                            label="UMKM" 
                            :options="$businessOptions"
                            placeholder="Cari UMKM..." 
                            :disabled="$isEdit"
                        />
                    </div>
                    <div class="mb-2">
                        <x-modal.searchable-select 
                            wire:model="member_id" 
                            name="member_id" 
                            label="Member" 
                            :options="$memberOptions"
                            placeholder="Cari Member..." 
                            :disabled="$isEdit"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="col-span-2">
                        <x-modal.input name="transaction_code" label="Transaction Code" :disabled="$isEdit" />
                    </div>
                    <div class="col-span-2">
                        <x-modal.input name="transaction_date" label="Tanggal Nota" type="date" :disabled="$isEdit" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="{{ $isEdit ? 'col-span-2' : '' }}">
                        <x-modal.input name="amount" label="Jumlah Nota" type="number" :disabled="$isEdit" />
                    </div>
                    
                    @if(!$isEdit)
                        <div>
                            <x-modal.input name="hpp" label="HPP" type="number" />
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <x-modal.input name="balance" label="Bonus Dibagi" type="number" :disabled="$isEdit" />
                    </div>
                    <div>
                        <x-modal.input name="bonus" label="Bonus Diterima" type="number" :disabled="$isEdit" />
                    </div>
                </div>

                @if($isEdit)
                    <div class="mt-2 p-3 bg-slate-50 border border-slate-200 rounded-lg flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-600">Persentase Bonus (Baris Ini)</span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 font-bold rounded-full text-sm">
                            {{ $BonusPercent ?? 0 }}%
                        </span>
                    </div>
                @endif
            </div>

            @if($isEdit)
                <x-slot name="footer">
                    <div class="flex justify-end w-full">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                            Close Detail
                        </button>
                    </div>
                </x-slot>
            @endif
        </x-modal.form-modal>
    @endif

    {{-- Import Modal --}}
    @if($isOpenImport)
        <x-modal.form-modal :formTitle="'Import Transactions'" action="storeData()" height="h-auto">
           <div class="grid gap-4 grid-cols-1 px-2 py-4 md:py-6">
                <div class="grid grid-cols-1 gap-2 text-black">
                    <div class="mb-0">
                        <label class="block mb-2.5 text-sm font-medium text-heading" for="file_input">Upload file</label>
                        <input name="file" id="file" wire:model="file" class="cursor-pointer bg-slate-50 border border-stone-500 text-heading text-sm rounded-md focus:ring-stone focus:border-stone block w-full shadow-xs placeholder:text-body p-2" type="file">
                        @error('file')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <p>Download template excel <a class="text-blue-500 underline" href="{{ asset('storage/templates/import_transaction.xlsx')}}">download here!!</a></p>
                </div>
            </div>
        </x-modal.form-modal>
    @endif
   
    <x-alerts.success/>
    <x-alerts.delete-confirmation/>
</div>