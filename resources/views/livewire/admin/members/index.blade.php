<div>
    {{-- header --}}
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Members" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search" placeholder="Search Members" />
                </div>
                <div>
                    <x-widget.button color="neutral" name="Add Member" action="openModal()" />
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
                        <x-table.th>Member Code</x-table.th>
                        <x-table.th>NIK</x-table.th>
                        <x-table.th>User</x-table.th>
                        <x-table.th>Parent Member</x-table.th>
                        <x-table.th>Phone</x-table.th>
                        <x-table.th>Gender</x-table.th>
                        <x-table.th>Address</x-table.th>
                        <x-table.th>Birth Date</x-table.th>
                        <x-table.th>NPWP</x-table.th>
                        <x-table.th>Province</x-table.th>
                        <x-table.th>Domicile</x-table.th>
                        <x-table.th>Bank Name</x-table.th>
                        <x-table.th>Account Number</x-table.th>
                        <x-table.th>Account Name</x-table.th>
                        <x-table.th>Profile Picture</x-table.th>
                        <x-table.th>Actions</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @forelse ($members as $item)
                        <x-table.tr>
                            <x-table.td>{{ $members->firstItem() + $loop->index }}</x-table.td>
                            <x-table.td>{{ $item->member_code }}</x-table.td>
                            <x-table.td>{{ $item->nik }}</x-table.td>
                            <x-table.td>{{ $item->user->name }}</x-table.td>
                            <x-table.td>{{ $item->parentMember->user->name ?? '-' }}</x-table.td>
                            <x-table.td>{{ $item->phone_number }}</x-table.td>
                            <x-table.td>{{ $item->gender }}</x-table.td>
                            <x-table.td>{{ $item->address }}</x-table.td>
                            <x-table.td>{{ $item->birth_date }}</x-table.td>
                            <x-table.td>{{ $item->npwp }}</x-table.td>
                            <x-table.td>{{ $item->province->name ?? '-' }}</x-table.td>
                            <x-table.td>{{ $item->domicile->name ?? '-' }}</x-table.td>
                            <x-table.td>{{ $item->bank_name }}</x-table.td>
                            <x-table.td>{{ $item->account_number }}</x-table.td>
                            <x-table.td>{{ $item->account_name }}</x-table.td>
                            <x-table.td>{{ $item->profile_picture }}</x-table.td>
                            <x-table.td>
                                <x-widget.button color="neutral" name="Edit" action="openModal({{ $item->id }})" />
                                <x-widget.button color="danger" name="Delete" action="confirmDelete({{ $item->id }})" />
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="5" class="text-center py-4">
                                No Members found.
                            </x-table.td>
                        </x-table.tr>
                    @endforelse
                </tbody>
            </x-table.table>
           @if ($members->hasPages())
                <div class="p-4">
                    {{ $members->links() }}
                </div>
            @endif
        </div>
    </div>

     <!-- Modal -->
    @if($isOpen)
       <x-modal.form-modal :formTitle="$member_id ? 'Edit Member' : 'Add Member'"  action="store()" >
           <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                <div class="col-span-2">
                    <label for="name" class="block mb-2.5 text-sm font-medium text-heading">Name</label>
                    <input 
                        type="text" 
                        wire:model.defer="name"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-md focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" 
                        placeholder="Contoh: Bali"
                    >
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="col-span-2">
                    <label for="address" class="block mb-2.5 text-sm font-medium text-heading">Address</label>
                    <input 
                        type="text" 
                        wire:model.defer="address"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-md focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" 
                        placeholder="Contoh: Jalan Sudirman No. 123"
                    >
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="col-span-2">
                    <label for="phone" class="block mb-2.5 text-sm font-medium text-heading">Phone</label>
                    <input 
                        type="number" 
                        wire:model.defer="phone"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-md focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" 
                        placeholder="Contoh: 08123456789"
                    >
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-modal.form-modal>
    @endif
   
    <x-alerts.success/>
    <x-alerts.delete-confirmation/>



</div>
