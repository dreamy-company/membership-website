<tr {{ $attributes->merge(['class' => 'border-b']) }} wire:key="{{ md5($slot) }}">
    {{ $slot }}
</tr>
