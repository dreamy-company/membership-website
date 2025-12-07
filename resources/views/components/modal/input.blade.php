@props(['name', 'label', 'type' => 'text', 'placeholder'])

<label for="{{ $name }}" class="block mb-2.5 text-sm font-medium text-heading">{{ $label }}</label>
<input 
    type="{{ $type }}" 
    wire:model.defer="{{ $name }}"
    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-md focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" 
    placeholder="{{ $placeholder }}"
        >
        @error($name)
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
@enderror
