<div>

    {{-- header --}}
    <div class="p-4 bg-white block sm:flex items-center justify-between lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Dashboard" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Hello,
                    {{ auth()->user()->name }}</h1>
            </div>
        </div>
    </div>
    <div class="flex h-full w-full flex-1 flex-col gap-0 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center gap-2">
                <div class="flex gap-2">
                    <flux:icon.star class="text-black" />
                    <flux:text size="xl">
                        Bonus Balance
                    </flux:text>
                </div>

                <flux:heading class="mb-1 text-3xl!">Rp. {{ number_format($bonusTotal->balance ?? 0, 0) }}
                </flux:heading>
                <flux:button href="{{ route('dashboard.withdrawals') }}" variant="primary"
                    class="bg-black text-white hover:bg-gray-700">
                    See Detail</flux:button>
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center gap-2">
                <div class="flex gap-2">
                    <flux:icon.banknotes class="text-black" />
                    <flux:text size="xl">
                        Total Transactions
                    </flux:text>
                </div>

                <flux:heading class="mb-1 text-3xl!">Rp. {{ number_format($transactionTotal ?? 0, 0) }}</flux:heading>
                <flux:button href="{{ route('dashboard.transactions') }}" variant="primary"
                    class="bg-black text-white hover:bg-gray-700">
                    See Detail</flux:button>
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center gap-2">
                <div class="flex gap-2">
                    <flux:icon.users class="text-black" />
                    <flux:text size="xl">
                        Total Members
                    </flux:text>
                </div>

                <flux:heading class="mb-1 text-3xl!">{{ $totalMembers }}</flux:heading>
                <flux:button href="{{ route('dashboard.members') }}" variant="primary"
                    class="bg-black text-white hover:bg-gray-700">See
                    Detail</flux:button>
            </div>
        </div>

        <div class="p-4 bg-white block sm:flex items-center justify-between lg:mt-1.5">
            <div class="w-full">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Transactions</h1>
                </div>
            </div>
        </div>

        {{-- table --}}
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-md border border-default">
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th>No</x-table.th>
                        <x-table.th>Member</x-table.th>
                        <x-table.th>UMKM</x-table.th>
                        <x-table.th>Transaction Code</x-table.th>
                        <x-table.th>Transaction Date</x-table.th>
                        <x-table.th>Time</x-table.th>
                        <x-table.th>Level</x-table.th>
                        <x-table.th>Percent</x-table.th>
                        <x-table.th>Bonus</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @forelse ($transactions as $item)
                        <x-table.tr>
                            <x-table.td>{{ $transactions->firstItem() + $loop->index }}</x-table.td>
                            <x-table.td>{{ $item->sourceMember->user->name }}</x-table.td>
                            <x-table.td>{{ $item->transaction->business->name }}</x-table.td>
                            <x-table.td>{{ $item->transaction->transaction_code }}</x-table.td>
                            <x-table.td>{{ $item->transaction->transaction_date }}</x-table.td>
                            <x-table.td>{{ date('H:i:s', strtotime($item->transaction->created_at)) }}</x-table.td>
                            <x-table.td>{{ $item->level }}</x-table.td>
                            <x-table.td>{{ number_format($item->percentage) }}</x-table.td>
                            <x-table.td>{{ number_format($item->amount) }}</x-table.td>
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
</div>
