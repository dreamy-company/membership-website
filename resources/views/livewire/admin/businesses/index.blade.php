<div>
    {{-- header --}}
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Businesses" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search" placeholder="Search Businesses" />
                </div>
                <div>
                    <x-widget.button color="neutral" name="Add Business" action="openModal()" />
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
                        <x-table.th>Name</x-table.th>
                        <x-table.th>Address</x-table.th>
                        <x-table.th>Phone</x-table.th>
                        <x-table.th>Actions</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @forelse ($businesses as $item)
                        <x-table.tr>
                            <x-table.td>{{ $businesses->firstItem() + $loop->index }}</x-table.td>
                            <x-table.td>{{ $item->name }}</x-table.td>
                            <x-table.td>{{ $item->address }}</x-table.td>
                            <x-table.td>{{ $item->phone }}</x-table.td>
                            <x-table.td>
                                <div class="flex gap-2">
                                    <x-widget.button-icon type="edit" action="openModal({{ $item->id }})" />
                                    <x-widget.button-icon type="delete" action="confirmDelete({{ $item->id }})" />
                                </div>
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="5" class="text-center py-4">
                                No businesses found.
                            </x-table.td>
                        </x-table.tr>
                    @endforelse
                </tbody>
            </x-table.table>
           @if ($businesses->hasPages())
                <div class="p-4">
                    {{ $businesses->links() }}
                </div>
            @endif
        </div>
    </div>

     <!-- Modal -->
    @if($isOpen)
       <x-modal.form-modal :formTitle="$business_id ? 'Edit Business' : 'Add Business'"  action="store()" height="h-auto">
           <div class="grid gap-4 grid-cols-2 py-4 px-2 md:py-6">
                <div class="col-span-2">
                    <x-modal.input label="Name" type="text" name="name" placeholder="Contoh: Toko Sumber Rejeki" />
                </div>
                <div class="col-span-2">
                    <x-modal.input label="Address" type="text" name="address" placeholder="Contoh: Jalan Sudirman No. 123" />
                </div>
                <div class="col-span-2">
                    <x-modal.input label="Phone" type="{{ $business_id ? 'tel' : 'number' }}" name="phone" placeholder="Contoh: 08123456789" />
                </div>
            </div>
        </x-modal.form-modal>
    @endif
   
    <x-alerts.success/>
    <x-alerts.delete-confirmation/>



</div>
