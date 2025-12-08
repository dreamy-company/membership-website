@props(['node'])

<div class="ml-4 border-l pl-4 py-2">

    {{-- Row utama --}}
    <div
        class="flex items-center justify-between gap-3 p-2 rounded border border-gray-200 w-full md:w-1/3 hover:bg-gray-100">

        <div class="flex gap-4">

            {{-- Profile --}}
            <img src="{{ $node['user']['profile_picture'] ?? 'https://i.pravatar.cc/60?u=' . $node['id'] }}"
                class="w-10 h-10 rounded-full border">

            {{-- Info --}}
            <div>
                <div class="font-semibold text-sm">{{ $node['member_code'] }}</div>
                <div class="text-gray-600 text-sm">
                    {{ $node['user']['name'] ?? 'Tanpa Nama' }}
                </div>
            </div>
        </div>

        @if ($node['level'] > 5)
            <div class="text-sm text-red-500 italic">Maks level tercapai</div>
        @endif

        @if ($node['level'] <= 5)

            {{-- Arrow and Count --}}
            <div class="flex items-center gap-2">
                <div class="w-5 cursor-pointer" wire:click.stop="toggleNode({{ $node['id'] }})">
                    @if ($node['loading'])
                        <svg class="w-5 h-5 animate-spin text-gray-600" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    @elseif ($node['fetched'] && empty($node['children']))
                        <span class="text-gray-400">•</span>
                    @else
                        <span
                            class="inline-block transition-transform duration-300 {{ $node['expanded'] ? 'rotate-90' : 'rotate-0' }}">
                            <svg class="w-5 h-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z" />
                            </svg>
                        </span>
                    @endif
                </div>
                @if (!empty($node['children']) && $node['level'] <= 5)
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                        {{ count($node['children']) }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- Children --}}
    @if ($node['expanded'])
        <div class="ml-6 border-l pl-4 mt-2 space-y-2">

            {{-- Loading --}}
            @if ($node['loading'])
                <div class="text-sm text-gray-500">Mengambil data member...</div>

                {{-- Tidak ada anak --}}
            @elseif (empty($node['children']))
                <div class="text-sm text-gray-500 italic">Tidak ada member di bawah ini.</div>

                {{-- List child --}}
            @else
                @foreach ($node['children'] as $child)
                    <x-tree-node :node="$child" />
                @endforeach
            @endif

        </div>
    @endif
</div>
