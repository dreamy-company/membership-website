<div>
    {{-- header --}}
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Transactions" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search"
                        placeholder="Search Transactions" />
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
                        <x-table.th>UMKM</x-table.th>
                        <x-table.th>Member</x-table.th>
                        <x-table.th>Transaction Code</x-table.th>
                        <x-table.th>Transaction Date</x-table.th>
                        <x-table.th>Amount</x-table.th>
                        <x-table.th>Hpp</x-table.th>
                        <x-table.th>Balance</x-table.th>
                        <x-table.th>Bonus</x-table.th>
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
    @if ($isOpen)
        <x-modal.form-modal :formTitle="$province_id ? 'Edit Province' : 'Add Province'" action="store()">
            <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                <div class="col-span-2">
                    <x-modal.input name="name" label="Nama Provinsi" placeholder="Contoh: Bali" />
                </div>
            </div>
        </x-modal.form-modal>
    @endif

    <x-alerts.success />
    <x-alerts.delete-confirmation />



</div>
