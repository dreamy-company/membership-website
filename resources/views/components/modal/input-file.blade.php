@props([
    'label' => 'Upload File', 
    'model', 
    'accept' => 'image/*,application/pdf/*'
])

<div class="mb-4">
    {{-- Label --}}
    <label class="block mb-2 text-sm font-medium text-gray-900">
        {{ $label }}
    </label>

    {{-- Standard Input File --}}
    <input 
        type="file" 
        wire:model="{{ $model }}" 
        accept="{{ $accept }}"
        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:border-blue-500 focus:ring-blue-500
               file:mr-4 file:py-2 file:px-4
               file:rounded-l-lg file:border-0
               file:text-sm file:font-semibold
               file:bg-neutral-900 file:text-white
               hover:file:bg-neutral-700"
    />

    {{-- Loading Indicator --}}
    <div wire:loading wire:target="{{ $model }}" class="mt-1">
        <span class="text-xs text-blue-600 font-medium animate-pulse">
            Sedang mengupload...
        </span>
    </div>

    {{-- Error Message (Opsional, sangat berguna) --}}
    @error($model)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>