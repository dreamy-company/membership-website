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
    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-md border border-default">
        <x-table.table>
            <x-table.thead>
                <x-table.tr>
                    <x-table.th>No</x-table.th>
                    <x-table.th>Member</x-table.th>
                    <x-table.th>Withdrawal Amount</x-table.th>
                    <x-table.th>Date</x-table.th>
                    <x-table.th>Payment Receipt</x-table.th>
                </x-table.tr>
            </x-table.thead>

            <tbody>
                @forelse ($withdrawals as $item)
                    <x-table.tr>
                        <x-table.td>{{ $withdrawals->firstItem() + $loop->index }}</x-table.td>
                        <x-table.td>{{ $item->member->user->name }}</x-table.td>
                        <x-table.td>{{ number_format($item->amount) }}</x-table.td>
                        <x-table.td>{{ $item->created_at->format('d M Y') }}</x-table.td>
                        <x-table.td>
                            <div class="flex items-center justify-center gap-2">
                                @if($item->payment_receipt)
                                    <a href="{{ asset('storage/' . $item->payment_receipt) }}" target="_blank" 
                                    class="inline-flex shrink-0 items-center justify-center p-2 text-blue-600 bg-white hover:bg-blue-50 rounded-lg transition-colors shadow-sm border border-blue-200" 
                                    title="Preview PDF">
                                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10V4a1 1 0 0 0-1-1H9.914a1 1 0 0 0-.707.293L5.293 7.207A1 1 0 0 0 5 7.914V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2M10 3v4a1 1 0 0 1-1 1H5m5 6h9m0 0-2-2m2 2-2 2"/>
                                        </svg>
                                    </a>
                                @endif
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

    @if ($isOpen)
        <x-modal.card-modal :formTitle="'Payment Receipt'">
            {{-- Payment Receipt --}}
            <div class="flex justify-center mt-4 mb-4">
                {{-- Container gambar dikasih Max Height & Scroll --}}
                <div class="max-h-[60vh] overflow-y-auto rounded-md border shadow-sm">
                    <img src="{{ asset('storage/' . $payment_receipt) }}" 
                        alt="Payment Receipt"
                        class="w-full h-auto block">
                </div>
            </div>
        </x-modal.card-modal>
    @endif


</div>

<x-alerts.success />
<x-alerts.delete-confirmation />

</div>
