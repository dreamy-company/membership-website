@props([
    'name', 
    'label', 
    'type' => 'text', 
    'placeholder' => '', 
    'disabled' => false,
    'value' => null, // optional default value
])

<label for="{{ $name }}" class="block mb-2.5 text-sm font-medium text-heading text-black">
    {!! $label !!}
</label>

<input 
    type="{{ $type }}" 
    id="{{ $name }}"
    wire:model.defer="{{ $name }}"
    value="{{ old($name, $value) }}"
    placeholder="{{ $placeholder }}"
    {{ $disabled ? 'disabled' : '' }}
    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-md focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body text-black"
/>

@error($name)
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
@enderror
