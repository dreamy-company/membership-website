@props(['label' => 'Upload File', 'model', 'old' => null, 'accept' => 'image/*,application/pdf/*'])

<div class="grid grid-cols-1 gap-2 mb-4">
    <label class="block mb-2.5 text-sm font-medium text-heading text-black">{{ $label }}</label>

    <div 
        class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md p-4 cursor-pointer hover:border-gray-400 transition"
        x-data
        @dragover.prevent="dragging=true"
        @dragleave.prevent="dragging=false"
        @drop.prevent="$refs.fileInput.files = $event.dataTransfer.files; $dispatch('input', $event.dataTransfer.files)"
    >
        <input 
            type="file" 
            wire:model="{{ $model }}" 
            accept="{{ $accept }}"
            class="hidden" 
            x-ref="fileInput"
        />

        {{-- Preview --}}
        <div class="mb-2 w-full flex justify-center">
            @if (isset($$model) && $$model instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) dd($$model);
                @if(str_contains($$model->getClientOriginalName(), '.pdf'))
                    <a href="{{ $$model->temporaryUrl() }}" target="_blank" class="text-blue-500 underline">Preview PDF</a>
                @else
                    <img src="{{ $$model->temporaryUrl() }}" alt="Preview" class="max-h-40 rounded-md border border-gray-300">
                @endif
            @elseif (!empty($$old))
                @if(str_contains($$old, '.pdf'))
                    <a href="{{ asset('storage/' . $$old) }}" target="_blank" class="text-blue-500 underline">Preview PDF</a>
                @else
                    <img src="{{ asset('storage/' . $$old) }}" alt="Old Image" class="max-h-40 rounded-md border border-gray-300">
                @endif
            @endif
        </div>

        <span class="text-gray-500 text-sm {{ (isset($$model) && $$model) || (!empty($$old)) ? 'hidden' : '' }}">
            Drag & drop a file here or click to select
        </span>
        <button type="button" class="mt-2 px-3 py-1 bg-gray-200 rounded {{ (isset($$model) && $$model) || (!empty($$old)) ? 'hidden' : '' }}" @click="$refs.fileInput.click()">
            Select File
        </button>

        <div wire:loading wire:target="{{ $model }}" class="text-gray-500 text-sm mt-2">
            Loading preview...
        </div>
    </div>
</div>
