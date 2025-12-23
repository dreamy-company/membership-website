@props(['node'])

<div class="ml-4 border-l pl-4 py-2">
    {{-- Row utama --}}
    <div class="flex items-center justify-between gap-3 p-2 rounded border border-gray-200 w-full md:w-2/3 min-w-[400px] hover:bg-gray-100 bg-white">
        <div class="flex gap-4">
            {{-- Profile --}}
            @if ($node['user']['profile_picture'])
                <img src="{{ asset("storage/{$node['user']['profile_picture']}") }}" alt="Profile"
                    class="w-10 h-10 object-cover rounded-full shadow-lg border-4 border-white">
            @else
                <div class="w-10 h-10 rounded-full shadow-lg border-4 border-white bg-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            @endif

            {{-- Info --}}
            <div>
                <div class="font-semibold text-sm text-black">{{ $node['member_code'] }}</div>
                <div class="text-black text-sm">{{ $node['user']['name'] ?? 'Tanpa Nama' }}</div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            <x-widget.button color="neutral" name="Add" action="openMemberModal({{ $node['user_id'] }})" />
            <x-widget.button color="warning" name="Edit" action="openModal({{ $node['id'] }})" />
            <x-widget.button color="neutral" name="Detail" action="openCardModal({{ $node['id'] }})" />
            
            <div class="w-5 cursor-pointer" wire:click.stop="toggleNode({{ $node['id'] }})">
                @if ($node['loading'])
                    <svg class="w-5 h-5 animate-spin text-gray-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                @elseif ($node['fetched'] && empty($node['children']))
                    <span class="text-gray-400">•</span>
                @else
                    <span class="inline-block transition-transform duration-300 {{ $node['expanded'] ? 'rotate-90' : 'rotate-0' }}">
                        <svg class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 20 20"><path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z" /></svg>
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Children Recursion --}}
    @if ($node['expanded'])
        <div class="ml-6 border-l pl-4 mt-2 space-y-2">
            @if ($node['loading'])
                <div class="text-sm text-gray-500">Mengambil data member...</div>
            @elseif (empty($node['children']))
                <div class="text-sm text-gray-500 italic">Tidak ada member di bawah ini.</div>
            @else
                @foreach ($node['children'] as $child)
                    {{-- REKURSIF PANGGIL DIRINYA SENDIRI (List) --}}
                    <x-tree-node-list :node="$child" />
                @endforeach
            @endif
        </div>
    @endif
</div>