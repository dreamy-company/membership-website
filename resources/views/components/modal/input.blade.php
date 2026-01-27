@props([
    'name', 
    'label', 
    'type' => 'text', 
    'placeholder' => '', 
    'disabled' => false,
    // 'value' tidak diperlukan jika menggunakan wire:model, Livewire akan mengaturnya otomatis
])

<div class="mb-4">
    <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-gray-900">
        {!! $label !!}
    </label>

    <input 
        type="{{ $type }}" 
        id="{{ $name }}"
        wire:model.defer="{{ $name }}"
        placeholder="{{ $placeholder }}"
        {{ $disabled ? 'disabled' : '' }}
        class="
            bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
            placeholder-gray-400
            focus:ring-blue-500 focus:border-blue-500 
            disabled:bg-gray-100 disabled:cursor-not-allowed
            [&::-webkit-calendar-picker-indicator]:opacity-100 [&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:block
        "
    />

    @error($name)
        <p class="text-red-500 text-sm mt-1 animate-pulse">{{ $message }}</p>
    @enderror
</div>