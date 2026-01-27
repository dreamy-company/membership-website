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
    <div class="table w-full mt-6 px-4 pb-4">
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
       <x-modal.form-modal :formTitle="$withdrawal_id ? 'Edit Withdrawal' : 'Add Withdrawal'"  action="store()" >
           <div class="py-4 px-2 md:py-6">
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="gap-2">
                        <x-modal.select name="member_id" label="Member">
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->user->name }}</option>
                            @endforeach
                        </x-modal.select>
                    </div>
                    <div class="gap-2">
                        <x-modal.input name="amount" label="Amount" type="number" placeholder="Contoh: 100000" />
                    </div>        
                </div>
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="gap-2">
                        <x-modal.input name="amount" label="Amount" type="number" placeholder="Contoh: 100000" />
                    </div>        
                    <div class="gap-2">
                        <x-modal.input name="date" label="Date" type="date" placeholder="Contoh: 2024-06-01" />
                    </div>        
                </div>
                <div class="grid grid-cols-1 gap-2 mb-4">
                    <label class="block mb-2.5 text-sm font-medium text-heading">Payment Receipt</label>
                    <div 
                        class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md p-4 cursor-pointer hover:border-gray-400 transition"
                        x-data
                        @dragover.prevent="dragging=true"
                        @dragleave.prevent="dragging=false"
                        @drop.prevent="$refs.fileInput.files = $event.dataTransfer.files; $dispatch('input', $event.dataTransfer.files)"
                    >
                        <input 
                            type="file" 
                            wire:model="payment_receipt" 
                            class="hidden" 
                            x-ref="fileInput"
                        />

                        {{-- Preview --}}
                        <div class="mb-2 w-full flex justify-center">
                            @if ($payment_receipt instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                <img src="{{ $payment_receipt->temporaryUrl() }}" alt="Preview" class="max-h-40 rounded-md border border-gray-300">
                            @elseif(!empty($old_payment_receipt))
                                <img src="{{ asset('storage/'.$old_payment_receipt) }}" alt="Old Image" class="max-h-40 rounded-md border border-gray-300">
                            @endif
                        </div>

                        <span class="text-gray-500 text-sm">
                            Drag & drop a file here or click to select
                        </span>
                        <button type="button" class="mt-2 px-3 py-1 bg-gray-200 rounded" @click="$refs.fileInput.click()">
                            Select File
                        </button>

                        <div wire:loading wire:target="payment_receipt" class="text-gray-500 text-sm mt-2">
                            Loading preview...
                        </div>
                    </div>
                </div>
            </div>
        </x-modal.form-modal>
    @endif
   
    <x-alerts.success/>
    <x-alerts.delete-confirmation/>


</div>
