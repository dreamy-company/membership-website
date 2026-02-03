@props([
    'action', 
    'type',        // 'add', 'edit', 'delete'
    'color' => null, // Opsional: jika kosong akan otomatis sesuai type
    'title' => null,  // Tooltip saat hover
    'visible' => true // <--- Default True (Tampil)
])

@php
    // 1. Definisi Icon SVG (Sesuai request kamu)
    $icons = [
        'add' => '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7.757v8.486M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>',
        
        'edit' => '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/></svg>',
        
        'delete' => '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/></svg>',

        'detail' => '<svg class="w-6 h-6 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/><path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>',
    ];

    // 2. Ambil Icon
    $selectedIcon = $icons[$type] ?? null;

    // 3. Auto-Color jika tidak diset manual
    if (!$color) {
        $color = match($type) {
            'delete' => 'danger',
            'edit'   => 'warning',
            'add'    => 'primary',
            'detail' => 'bg-blue-900',
            default  => 'primary'
        };
    }

    // 4. Default Title untuk Tooltip (Accessibility)
    $defaultTitle = match($type) {
        'add'    => 'Tambah Data',
        'edit'   => 'Edit Data',
        'delete' => 'Hapus Data',
        'detail' => 'Lihat Detail',
        default  => 'Action'
    };
    $ariaLabel = $title ?? $defaultTitle;

    // 5. Palette Warna
    $colors = [
        'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-300 text-white',
        'primary' => 'bg-neutral-900 hover:bg-neutral-700 focus:ring-neutral-300 text-white',
        'detail' => 'bg-blue-900 hover:bg-blue-700 focus:ring-blue-300 text-white',
        'warning' => 'bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-300 text-white',
        'neutral' => 'bg-white border-gray-300 border hover:bg-gray-50 focus:ring-gray-200 text-gray-700'
    ];

    $colorClass = $colors[$color] ?? $colors['primary'];
@endphp

@if ($visible)
    <button
        type="button" 
        wire:click="{{ $action }}"
        title="{{ $ariaLabel }}"
        aria-label="{{ $ariaLabel }}"
        class="{{ $colorClass }}  {{ auth()->user()->role !== 'admin' ? 'hidden' : '' }}
            p-2 inline-flex items-center justify-center
            border border-transparent shadow-sm rounded-lg 
            focus:outline-none focus:ring-2 focus:ring-offset-1
            transition-all duration-150 ease-in-out"
    >
        {!! $selectedIcon !!}
    </button>
@endif