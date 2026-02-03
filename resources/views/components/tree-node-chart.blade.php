@props(['node'])

<li>
    <div class="member-card cursor-pointer group bg-white" wire:click.stop="toggleNode({{ $node['id'] }})">
        <div class="flex flex-col items-center gap-2 p-2">
            {{-- Foto --}}
            <div class="relative">
                @if ($node['user']['profile_picture'])
                    <img src="{{ asset("storage/{$node['user']['profile_picture']}") }}" alt="Profile" class="w-16 h-16 object-cover rounded-full shadow-md border-2 border-gray-200">
                @else
                    <div class="w-16 h-16 rounded-full shadow-md border-2 border-gray-200 bg-gray-100 flex items-center justify-center text-gray-400">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    </div>
                @endif
                {{-- Count Badge --}}
                @if (!empty($node['children']) || ($node['loading'] ?? false))
                   <span class="absolute -bottom-2 -right-2 bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full border border-white">
                        {{ count($node['children']) > 0 ? count($node['children']) : '+' }}
                   </span>
                @endif
            </div>

            {{-- Text Info --}}
            <div class="text-center">
                <div class="font-bold text-gray-800 text-sm truncate max-w-[150px]">{{ $node['user']['name'] ?? 'Tanpa Nama' }}</div>
                <div class="text-xs text-blue-600 font-mono bg-blue-50 px-2 py-0.5 rounded mt-1 inline-block">{{ $node['member_code'] }}</div>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center justify-center gap-1 mt-2 border-t pt-2 w-full">
                <button wire:click.stop="openMemberModal({{ $node['user_id'] }})" class="p-1 rounded bg-green-50 text-green-600 hover:bg-green-100 transition" title="Add"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button>
                {{-- Tombol Edit --}}
                <button wire:click.stop="openModal({{ $node['id'] }})" 
                        class="p-1 rounded bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition {{ auth()->user()->role !== 'admin' ? 'hidden' : '' }}" 
                        title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>

                {{-- Tombol Detail --}}
                <button wire:click.stop="openCardModal({{ $node['id'] }})" 
                        class="p-1 rounded bg-blue-50 text-blue-600 hover:bg-blue-100 transition {{ auth()->user()->role !== 'admin' ? 'hidden' : '' }}" 
                        title="Detail">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>
            </div>

            {{-- Loading --}}
            @if ($node['loading'])
                <div class="absolute inset-0 bg-white/80 flex items-center justify-center rounded-lg">
                    <svg class="w-6 h-6 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
            @endif
        </div>
    </div>

    {{-- Children Recursion --}}
    @if ($node['expanded'] && !empty($node['children']))
        <ul>
            @foreach ($node['children'] as $child)
                {{-- REKURSIF PANGGIL DIRINYA SENDIRI (Chart) --}}
                <x-tree-node-chart :node="$child" />
            @endforeach
        </ul>
    @endif
</li>