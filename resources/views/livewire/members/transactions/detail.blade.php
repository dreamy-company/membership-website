<div>
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Transaction Detail" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Detail for Transaction: {{ $transactionCode }}</h1>
            </div>
        </div>
    </div>

    <div class="w-full mt-6 px-4 pb-4">
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-md border border-default">
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th>Sales Number</x-table.th>
                        <x-table.th>Sales Date</x-table.th>
                        <x-table.th>Customer Code</x-table.th>
                        <x-table.th>Customer Description</x-table.th>
                        <x-table.th>Total</x-table.th>
                        <x-table.th>Source</x-table.th>
                    </x-table.tr>
                </x-table.thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <x-table.tr>
                            <x-table.td>{{ $sale->SalesNumber }}</x-table.td>
                            <x-table.td>{{ $sale->SalesDate }}</x-table.td>
                            <x-table.td>{{ $sale->CustCode }}</x-table.td>
                            <x-table.td>{{ $sale->CustDesc }}</x-table.td>
                            <x-table.td>{{ number_format($sale->Total, 0, ',', '.') }}</x-table.td>
                            <x-table.td>{{ $sale->Sumber }}</x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="6" class="text-center py-4">No sales data found for this transaction code.</x-table.td>
                        </x-table.tr>
                    @endforelse
                </tbody>
            </x-table.table>
        </div>
        <div class="mt-4">
            <a href="{{ route('member.transactions') }}" class="text-blue-500 hover:underline">&larr; Back to Transactions</a>
        </div>
    </div>
</div>
