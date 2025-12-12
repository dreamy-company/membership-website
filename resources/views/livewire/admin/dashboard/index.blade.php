<div>
    {{-- header --}}
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 mb-2">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Admin Dashboard" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search" placeholder="Search Members" />
                </div>
            </div>
        </div>
    </div>

    @if ($members->count() > 0)
       <div class="w-full mt-6 px-4 pb-4 mb-4">
            <div class="overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-md border border-default">
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
    @endif

    <div class="grid grid-cols-4 gap-4">
        <x-dashboard.card
            title="Total Transactions"
            :total="$transactionsCount"
            route="admin.transactions" />
        <x-dashboard.card
            title="Total Members"
            :total="$membersCount"
            route="admin.members" />
        {{-- <x-dashboard.card
            title="Total Users"
            :total="$usersCount "
            route="admin.users" /> --}}
        <x-dashboard.card
            title="Total UMKM"
            :total="$businessesCount"
            route="admin.businesses" />
        <x-dashboard.card
            title="Total Provinces"
            :total="$provincesCount"
            route="admin.provinces" />
    </div>
    
</div>
