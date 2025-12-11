@props(['formTitle', 'action'])

<div id="crud-modal" class="fixed inset-0 bg-black/30 z-50 flex justify-center items-center overflow-y-auto ">
    <div
        class="relative p-4 w-full max-w-2xl">
        {{ $slot }}
    </div>
</div>
