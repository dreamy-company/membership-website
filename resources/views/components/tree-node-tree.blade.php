@props(['node'])

<li class="relative">
    {{-- Garis horizontal penghubung (connector) --}}
    {{-- Hanya muncul jika ini adalah children (bukan root level 1) --}}
    @if(($node['level'] ?? 1) > 1)
        <div class="absolute -left-[20px] top-4 w-5 border-t border-dashed border-gray-300"></div>
    @endif

    <div class="group flex items-center py-1">
        
        {{-- Toggle Icon & Folder Icon --}}
        <div class="flex items-center gap-2 cursor-pointer select-none" wire:click.stop="toggleNode({{ $node['id'] }})">
            
            {{-- Icon Expand/Collapse --}}
            <div class="w-5 flex justify-center text-gray-500 hover:text-gray-800">
                @if ($node['loading'])
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                @elseif (empty($node['children']) && $node['fetched'])
                    {{-- Dot jika tidak ada anak --}}
                    <div class="w-1.5 h-1.5 rounded-full bg-gray-300"></div>
                @else
                    {{-- Chevron Arrow --}}
                    <svg class="w-4 h-4 transition-transform {{ $node['expanded'] ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                @endif
            </div>

            {{-- Icon Folder/User --}}
            <div class="text-yellow-500">
                @if($node['expanded'])
                    {{-- Folder Open --}}
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg>
                @else
                    {{-- Folder Closed --}}
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg>
                @endif
            </div>

            {{-- Profile Image (Mini) --}}
            @if ($node['user']['profile_picture'])
                <img src="{{ asset("storage/{$node['user']['profile_picture']}") }}" class="w-5 h-5 rounded-full object-cover border border-gray-200">
            @endif

            {{-- Text Content --}}
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-700 font-medium group-hover:text-blue-600 transition">
                    {{ $node['user']['name'] ?? 'Tanpa Nama' }}
                </span>
                <span class="text-xs text-gray-400 bg-gray-100 px-1.5 rounded">
                    {{ $node['member_code'] }}
                </span>
            </div>
        </div>

        {{-- Action Buttons (Hidden by default, show on hover) --}}
        <div class="hidden group-hover:flex items-center gap-1 ml-4 opacity-0 group-hover:opacity-100 transition-opacity">
            <button wire:click.stop="openMemberModal({{ $node['user_id'] }})" title="Add Child" class="text-green-600 hover:bg-green-50 p-1 rounded"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button>
            <button wire:click.stop="openModal({{ $node['id'] }})" title="Edit" class="text-yellow-600 hover:bg-yellow-50 p-1 rounded"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
            <button wire:click.stop="openCardModal({{ $node['id'] }})" title="Detail" class="text-blue-600 hover:bg-blue-50 p-1 rounded"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
        </div>
    </div>

    {{-- Children Container --}}
    @if ($node['expanded'])
        <ul>
            @if (empty($node['children']) && !$node['loading'])
                <li class="pl-8 py-1 text-xs text-gray-400 italic">No members</li>
            @else
                @foreach ($node['children'] as $child)
                    <x-tree-node-tree :node="$child" />
                @endforeach
            @endif
        </ul>
    @endif
</li>