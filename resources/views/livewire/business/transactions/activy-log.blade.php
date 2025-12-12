<div>
    {{-- header --}}
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Activities Log" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search" placeholder="Search Activities Log" />
                </div>
                {{-- <div>
                    <x-widget.button color="neutral" name="Add Transaction" action="openModal()" />
                    <x-widget.button color="neutral" name="Import Transactions" action="openImportModal()" />
                    <x-widget.button color="neutral" name="Activity Log" action="redirectToActivityLog()" />
                </div> --}}
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
                        <x-table.th>Name</x-table.th>
                        <x-table.th>Type</x-table.th>
                        <x-table.th>Description</x-table.th>
                        <x-table.th>Date</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @forelse ($activities as $item)
                        <x-table.tr>
                            <x-table.td>{{ $activities->firstItem() + $loop->index }}</x-table.td>
                            <x-table.td>{{ $item->user->name }}</x-table.td>
                            <x-table.td>{{ $item->type }}</x-table.td>
                            <x-table.td>{{ $item->description }}</x-table.td>
                            <x-table.td>{{ $item->created_at->format('j M Y')}} ({{ $item->created_at->diffForHumans() }})</x-table.td>
                            {{-- <x-table.td>
                                <x-widget.button color="neutral" name="Edit" action="openModal({{ $item->id }})" />
                                <x-widget.button color="danger" name="Delete" action="confirmDelete({{ $item->id }})" />
                            </x-table.td> --}}
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="5" class="text-center py-4">
                                No Activity Logs found.
                            </x-table.td>
                        </x-table.tr>
                    @endforelse
                </tbody>
            </x-table.table>
           @if ($activities->hasPages())
                <div class="p-4">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
