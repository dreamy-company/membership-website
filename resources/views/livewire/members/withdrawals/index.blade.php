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
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search"
                        placeholder="Search Withdrawals" />
                </div>
            </div>
        </div>
    </div>

    {{-- @dd($members) --}}

    {{-- table --}}
    <div class="table w-full mt-6 px-4 pb-4">
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-md border border-default">
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th>No</x-table.th>
                        <x-table.th>Member</x-table.th>
                        <x-table.th>Withdrawal Amount</x-table.th>
                        <x-table.th>Payment Receipt</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @forelse ($withdrawals as $item)
                        <x-table.tr>
                            <x-table.td>{{ $withdrawals->firstItem() + $loop->index }}</x-table.td>
                            <x-table.td>{{ $item->member->user->name }}</x-table.td>
                            <x-table.td>{{ number_format($item->withdrawal_amount) }}</x-table.td>
                            <x-table.td>{{ $item->payment_receipt }}</x-table.td>
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


</div>

<x-alerts.success />
<x-alerts.delete-confirmation />

</div>
