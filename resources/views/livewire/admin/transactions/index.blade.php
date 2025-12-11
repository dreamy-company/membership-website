<div>
    {{-- header --}}
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
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
                    <x-widget.button color="neutral" name="Import Transactions" action="openImportModal()" />
                    <x-widget.button color="neutral" name="Activity Log" action="redirectToActivityLog()" />
                </div>
            </div>
        </div>
    </div>

    {{-- table --}}
    <div class="table w-full mt-6 px-4 pb-4">
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-md border border-default">
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th>No</x-table.th>
                        <x-table.th>UMKM</x-table.th>
                        <x-table.th>Member</x-table.th>
                        <x-table.th>Transaction Code</x-table.th>
                        <x-table.th>Transaction Date</x-table.th>
                        <x-table.th>Amount</x-table.th>
                        <x-table.th>Hpp</x-table.th>
                        <x-table.th>Balance</x-table.th>
                        <x-table.th>Bonus</x-table.th>
                        <x-table.th>Actions</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @forelse ($transactions as $item)
                        <x-table.tr>
                            <x-table.td>{{ $transactions->firstItem() + $loop->index }}</x-table.td>
                            <x-table.td>{{ $item->business->name }}</x-table.td>
                            <x-table.td>{{ $item->member->user->name }}</x-table.td>
                            <x-table.td>{{ $item->transaction_code }}</x-table.td>
                            <x-table.td>{{ $item->transaction_date }}</x-table.td>
                            <x-table.td>{{ number_format($item->amount) }}</x-table.td>
                            <x-table.td>{{ number_format($item->hpp) }}</x-table.td>
                            <x-table.td>{{ number_format($item->balance) }}</x-table.td>
                            <x-table.td>{{ number_format($item->bonus) }}</x-table.td>
                            <x-table.td>
                                <x-widget.button color="neutral" name="Edit" action="openModal({{ $item->id }})" />
                                <x-widget.button color="danger" name="Delete" action="confirmDelete({{ $item->id }})" />
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="5" class="text-center py-4">
                                No Transactions found.
                            </x-table.td>
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

     <!-- Modal -->
    @if($isOpen)
        <x-modal.form-modal :formTitle="$transaction_id ? 'Edit Transaction' : 'Add Transaction'"  action="store()" >
           <div class="grid gap-4 grid-cols-1 py-4 md:py-6">
                <div class="grid grid-cols-2 gap-2">
                    <div class="mb-2">
                        <x-modal.select name="business_id" label="UMKM">
                            @foreach($businesses as $business)
                                <option value="{{ $business->id }}">
                                    {{ $business->name }}
                                </option>
                            @endforeach
                        </x-modal.select>
                    </div>
                    <div class="mb-2">
                        <x-modal.select name="member_id" label="Member">
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">
                                    {{ $member->user->name }}
                                </option>
                            @endforeach
                        </x-modal.select>
                    </div>
                </div>
                <div class="grid grid-cols-2">
                    <div class="col-span-2">
                        <x-modal.input name="transaction_code" label="Transaction Code" placeholder="Enter transaction code" />
                    </div>
                    <div class="col-span-2">
                        <x-modal.input name="transaction_date" label="Transaction Date" type="date" placeholder="Select transaction date" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="mb-2">
                        <x-modal.input name="amount" label="Amount" type="number" placeholder="Enter amount" />
                    </div>
                    <div class="mb-2">
                        <x-modal.input name="hpp" label="HPP" type="number" placeholder="Enter HPP" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="mb-2">
                        <x-modal.input name="balance" label="Balance" type="number" placeholder="Enter balance" />
                    </div>
                    <div class="mb-2">
                        <x-modal.input name="bonus" label="Bonus" type="number" placeholder="Enter bonus" />
                    </div>
                </div>
            </div>
        </x-modal.form-modal>
    @endif

    <!-- Modal -->
    @if($isOpenImport)
        <x-modal.form-modal :formTitle="'Import Transactions'" action="storeData()">
           <div class="grid gap-4 grid-cols-1 py-4 md:py-6">
                    <div class="grid grid-cols-1 gap-2">
                        <div class="mb-0">
                            <label class="block mb-2.5 text-sm font-medium text-heading" for="file_input">Upload file</label>
                            <input name="file" id="file" wire:model="file" class="cursor-pointer bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full shadow-xs placeholder:text-body" type="file">
                            @error('file')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <p>Download template excel <a class="text-blue-500 underline" href="{{  asset('storage/templates/import_transaction.xlsx')}}">download here!!</a></p>
                    </div>
            </div>
        </x-modal.form-modal>
    @endif
   
    <x-alerts.success/>
    <x-alerts.delete-confirmation/>


</div>
