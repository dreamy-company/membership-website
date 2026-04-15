<div>
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Sales" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>

            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search" placeholder="Search Sales" />
                </div>
            </div>
        </div>
    </div>

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
            <div class="flex items-end">
                <button type="button" wire:click="resetFilters" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <div class="w-full mt-6 px-4 pb-4">
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-md border border-default">
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th>No</x-table.th>
                        <x-table.th class="whitespace-nowrap">UMKM Name</x-table.th>
                        <x-table.th>Member</x-table.th>
                        <x-table.th>Bill Number</x-table.th>
                        <x-table.th>Bill Date</x-table.th>
                        <x-table.th>Total Invoice</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @forelse ($sales as $item)
                        <x-table.tr>
                            <x-table.td>{{ $sales->firstItem() + $loop->index }}</x-table.td>
                            <x-table.td class="whitespace-nowrap">{{ $item->business->name ?? '-' }}</x-table.td>
                            <x-table.td>{{ $item->transaction->member->user->name ?? $item->CustDesc ?? '-' }}</x-table.td>
                            <x-table.td>{{ $item->SalesNumber ?? $item->TransactionCode ?? '-' }}</x-table.td>
                            <x-table.td>{{ $item->SalesDate ?? '-' }}</x-table.td>
                            <x-table.td>{{ number_format($item->TotalInvoice ?? 0, 0, ',', '.') }}</x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="6" class="text-center py-4">No sales found.</x-table.td>
                        </x-table.tr>
                    @endforelse
                </tbody>

                @if($sales->count() > 0)
                    <tfoot class="bg-gray-50 border-t border-gray-200 font-bold text-gray-800">
                        <x-table.tr>
                            <x-table.td colspan="5" class="text-right">TOTAL (Halaman Ini) :</x-table.td>
                            <x-table.td>Rp {{ number_format($sales->sum('TotalInvoice'), 0, ',', '.') }}</x-table.td>
                        </x-table.tr>
                    </tfoot>
                @endif
            </x-table.table>

            @if ($sales->hasPages())
                <div class="p-4 bg-white border-t border-gray-200">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>
</div>