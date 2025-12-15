@php
    $isActive = request()->routeIs($route);
@endphp

<li>
    <a
        href="{{ route($route) }}"
        wire:navigate
        class="flex items-center gap-3 px-3 py-2 rounded-md text-sm
        {{ $isActive
            ? 'bg-blue-50 text-blue-600 font-medium'
            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
        }}"
    >
        {{-- Icon --}}
        <x-heroicon-o-{{ $icon }} class="w-5 h-5" />

        <span class="w-ful">{{ $label }}</span>
    </a>
</li>
