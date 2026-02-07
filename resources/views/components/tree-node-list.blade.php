@props(['node'])

<div class="ml-2 sm:ml-4 border-l border-gray-300 pl-2 sm:pl-4 py-2 relative">
    
    {{-- Garis konektor --}}
    <div class="absolute top-6 left-0 w-2 sm:w-4 border-t border-gray-300"></div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-3 p-3 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 shadow-sm transition-all w-full md:max-w-xl">
        
        {{-- KIRI: INFO --}}
        <div class="flex items-center gap-3 min-w-0">
            <div class="shrink-0">
                @if ($node['user']['profile_picture'])
                    <img src="{{ asset("storage/{$node['user']['profile_picture']}") }}" alt="Profile"
                        class="w-10 h-10 object-cover rounded-full shadow-sm border border-gray-100">
                @else
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center border border-gray-200 text-gray-400">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    </div>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <div class="font-bold text-sm text-gray-900 truncate">
                    {{ $node['member_code'] }}
                </div>
                <div class="text-gray-600 text-xs sm:text-sm truncate">
                    {{ $node['user']['name'] ?? 'Tanpa Nama' }}
                </div>
            </div>
        </div>

        {{-- KANAN: ACTIONS --}}
        {{-- Gunakan flex-wrap agar icon turun jika layar sangat sempit --}}
        <div class="flex items-center justify-end gap-2 flex-wrap sm:flex-nowrap mt-2 sm:mt-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-gray-100">
            
            <x-widget.button-icon type="add" action="openMemberModal({{ $node['user_id'] }})" title="Tambah"/>
            <x-widget.button-icon type="edit" action="openModal({{ $node['id'] }})" title="Edit" :visible="auth()->user()->role === 'admin'" />
            <x-widget.button-icon type="detail" action="openCardModal({{ $node['id'] }})" title="Detail" :visible="auth()->user()->role === 'admin'" />
            
            {{-- Toggle Button --}}
            <div class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 cursor-pointer shrink-0" wire:click.stop="toggleNode({{ $node['id'] }})">
                @if ($node['loading'])
                    <svg class="w-4 h-4 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                @elseif ($node['fetched'] && empty($node['children']))
                    <span class="text-gray-300 text-2xl leading-none">•</span>
                @else
                    <svg class="w-5 h-5 text-gray-500 transition-transform duration-200 {{ $node['expanded'] ? 'rotate-90' : 'rotate-0' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                @endif
            </div>
        </div>
    </div>

    @if ($node['expanded'])
        <div class="ml-2 sm:ml-6 mt-2 space-y-2 border-l border-gray-200 pl-2 sm:pl-4 transition-all duration-300">
            @if ($node['loading'])
                <div class="text-xs text-gray-400 py-2 pl-2 animate-pulse">Memuat...</div>
            @elseif (empty($node['children']))
                <div class="text-xs text-gray-400 py-2 pl-2 italic">Kosong.</div>
            @else
                @foreach ($node['children'] as $child)
                    <x-tree-node-list :node="$child" />
                @endforeach
            @endif
        </div>
    @endif
</div>