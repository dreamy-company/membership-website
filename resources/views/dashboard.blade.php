<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center gap-2">
                <div class="flex gap-2">
                    <flux:icon.star />
                    <flux:text size="xl">
                        Bonus
                    </flux:text>
                </div>

                <flux:heading class="mb-1 text-3xl!">Rp. 1.000.000</flux:heading>
                <flux:button variant="primary">See Detail</flux:button>
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center gap-2">
                <div class="flex gap-2">
                    <flux:icon.banknotes />
                    <flux:text size="xl">
                        Total Transactions
                    </flux:text>
                </div>

                <flux:heading class="mb-1 text-3xl!">Rp. 100.000.000</flux:heading>
                <flux:button variant="primary">See Detail</flux:button>
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center gap-2">
                <div class="flex gap-2">
                    <flux:icon.users />
                    <flux:text size="xl">
                        Members
                    </flux:text>
                </div>

                <flux:heading class="mb-1 text-3xl!">20</flux:heading>
                <flux:button variant="primary">See Detail</flux:button>
            </div>
        </div>
        <div
            class="relative flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="relative h-full overflow-y-auto overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                <table class="w-full text-sm text-left rtl:text-right text-body">
                    <thead class="text-sm text-body bg-neutral-secondary-medium border-b border-default-medium">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">
                                No
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Product name
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                <div class="flex items-center">
                                    Color
                                    <a href="#">
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                <div class="flex items-center">
                                    Category
                                    <a href="#">
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                <div class="flex items-center">
                                    Price
                                    <a href="#">
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 1; $i <= 20; $i++)
                            <tr class="bg-neutral-primary-soft border-b  border-default">
                                <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                    {{ $i }}
                                </th>
                                <th class="px-6 py-4">
                                    Apple MacBook Pro 17"
                                </th>
                                <td class="px-6 py-4">
                                    Silver
                                </td>
                                <td class="px-6 py-4">
                                    Laptop
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-layouts.app>
