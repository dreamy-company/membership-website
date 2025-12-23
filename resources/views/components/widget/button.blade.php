@props(['color', 'name', 'action', 'icon'])

@php
    $colors = [
        'danger' => [
            'bg' => 'bg-red-600',
            'hover' => 'hover:bg-red-700',
            'ring' => 'focus:ring-red-300',
        ],
        'primary' => [
            'bg' => 'bg-neutral-900',
            'hover' => 'hover:bg-neutral-700',
            'ring' => 'focus:ring-neutral-300',
        ],
        'secondary' => [
            'bg' => 'bg-blue-900',
            'hover' => 'hover:bg-blue-800',
            'ring' => 'focus:ring-blue-600',
        ],
        'warning' => [
            'bg' => 'bg-yellow-500',
            'hover' => 'hover:bg-yellow-600',
            'ring' => 'focus:ring-yellow-300',
        ],
    ];

    $c = $colors[$color] ?? $colors['primary'];
@endphp

<button
    class="text-white {{ $c['bg'] }} {{ $c['hover'] }} {{ $c['ring'] }}
           border border-transparent shadow-xs font-medium leading-5 
           text-sm px-4 py-1.5 focus:outline-none rounded-md cursor-pointer mb-1"
    type="button" wire:click="{{ $action }}">
    {{ $name }}
    @if (isset($icon))
        {!! $icon !!}
    @endif
</button>
