<div class="bg-gray-50 min-h-screen">
    <div class="p-4 bg-white border-b border-gray-200">
        <div class="w-full">
            <div class="mb-1">
                <x-dashboard.breadcrumbs title="Transaction Detail" />
                @if($memberInfo)
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ $memberInfo->member_code }} - {{ $memberInfo->user->name ?? 'N/A' }}
                    </h1>
                @else
                    <h1 class="text-2xl font-bold text-gray-800">Transaction Detail</h1>
                @endif
            </div>
        </div>
    </div>

    <div class="p-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <x-table.table>
                    <x-table.thead class="bg-gray-100">
                        <x-table.tr>
                            <x-table.th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</x-table.th>
                            <x-table.th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam</x-table.th>
                            <x-table.th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toko</x-table.th>
                            <x-table.th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Nota</x-table.th>
                            <x-table.th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Belanja</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($sales as $sale)
                            <x-table.tr class="hover:bg-gray-50">
                                <x-table.td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::parse($sale->SalesDate)->format('d/m/Y') }}</x-table.td>
                                <x-table.td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $sale->SalesTime }}</x-table.td>
                                <x-table.td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $sale->business->name ?? 'N/A' }}</x-table.td>
                                <x-table.td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $sale->SalesNumber }}</x-table.td>
                                <x-table.td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium text-right">{{ number_format($sale->TotalCost, 0, ',', '.') }}</x-table.td>
                            </x-table.tr>
                        @empty
                            <x-table.tr>
                                <x-table.td colspan="5" class="text-center py-10 text-gray-500">No sales data found for this transaction code.</x-table.td>
                            </x-table.tr>
                        @endforelse
                    </tbody>
                    @if($sales->count() > 0)
                    <tfoot class="bg-gray-100">
                        <tr class="font-semibold text-gray-900">
                            <th scope="row" colspan="4" class="px-6 py-4 text-base text-right font-bold text-gray-700">Total Belanja</th>
                            <td class="px-6 py-4 text-right text-lg font-bold text-gray-900">{{ number_format($totalBelanja, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </x-table.table>
            </div>
        </div>
        <div class="mt-6 text-center">
            <a href="{{ route('member.transactions') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Transactions
            </a>
        </div>
    </div>
</div>
