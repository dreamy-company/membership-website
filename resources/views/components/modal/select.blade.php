@props(['name', 'label'])


<label class="block mb-2 text-sm font-medium">{!! $label !!}</label>
<select wire:model="{{ $name }}" class="w-full border px-3 py-2 rounded-md text-black">
    <option value="">Choose a {!! $label !!}</option>
    {{ $slot }}
</select>
@error($name)
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
@enderror
