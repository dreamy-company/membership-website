<div>
    {{-- header --}}
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Provinces" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                {{-- <div class="flex items-center mb-4 sm:mb-0">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search"
                        placeholder="Search Members" />
                </div> --}}
                <div>
                    <x-widget.button color="neutral" name="Add Member" action="openModal()" />
                </div>
            </div>
        </div>
    </div>

    {{-- @dd($members) --}}

    {{-- Tree List --}}
    <div class="p-6">
        <div class="space-y-4">
            @forelse ($members as $member)
                <x-tree-node :node="$member" />
            @empty
                <p class="text-center py-4 text-gray-500">Tidak ada member ditemukan.</p>
            @endforelse
        </div>
    </div>

    <x-alerts.success />
    <x-alerts.delete-confirmation />



</div>
