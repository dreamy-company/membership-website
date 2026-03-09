@props(['disabled' => false, 'name', 'label' => null])

<div class="mb-2">
    @if ($label)
        <label for="{{ $name }}" class="block mb-1 text-sm font-medium text-gray-900">
            {!! $label !!}
        </label>
    @endif
    
    <select 
        id="{{ $name }}" 
        name="{{ $name }}" 
        wire:model="{{ $name }}"
        {{-- KUNCI PERBAIKANNYA ADA DI SINI: --}}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 disabled:bg-gray-200 disabled:text-gray-500 disabled:cursor-not-allowed']) }}
    >
        <option value="">-- Pilih --</option>
        {{ $slot }}
    </select>
</div>