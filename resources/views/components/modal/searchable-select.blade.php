@props([
    'name', 
    'label', 
    'options' => [], 
    'placeholder' => 'Pilih opsi...',
    'liveUpdates' => false,
    'disabled' => false
])

<div 
    x-data="{
        open: false,
        search: '',
        selected: @entangle($attributes->wire('model')), 
        options: {{ json_encode($options) }},
        isDisabled: {{ var_export((bool)$disabled, true) }}, {{-- Konversi ke boolean JS --}}
        isLive: {{ var_export($liveUpdates, true) }}, 

        get selectedLabel() {
            if (!this.selected) return '';
            let found = this.options.find(o => o.value == this.selected);
            return found ? found.label : '';
        },

        get filteredOptions() {
            if (this.search === '') return this.options;
            return this.options.filter(option => {
                return option.label.toLowerCase().includes(this.search.toLowerCase());
            });
        },

        toggle() {
            if (this.isDisabled) return; {{-- Mencegah buka jika disabled --}}
            this.open = !this.open;
            if(this.open) $nextTick(() => $refs.searchInput.focus());
        },

        select(value) {
            if (this.isDisabled) return; {{-- Mencegah pilih jika disabled --}}
            this.selected = value;
            this.open = false;
            this.search = '';

            if (this.isLive) {
                 @this.set('{{ $attributes->wire('model')->value() }}', value); 
            }
        }
    }" 
    class="relative w-full mb-2"
>
    @if($label)
        <label class="block mb-2 text-sm font-medium text-gray-900">{{ $label }}</label>
    @endif
    
    <div class="relative">
        <button 
            type="button" 
            @click="toggle()" 
            :disabled="isDisabled"
            :class="isDisabled ? 'bg-gray-100 cursor-not-allowed opacity-75 border-gray-200' : 'bg-gray-50 border-gray-300 cursor-pointer'"
            class="w-full border text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 text-left flex justify-between items-center transition-all"
        >
            <span x-text="selected ? selectedLabel : '{{ $placeholder }}'" :class="selected ? 'text-gray-900' : 'text-gray-400'"></span>
            
            {{-- Sembunyikan icon panah jika disabled agar lebih bersih --}}
            <svg x-show="!isDisabled" class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- Dropdown List --}}
        <div x-show="open" @click.away="open = false" class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm" style="display: none;">
            <div class="sticky top-0 z-10 bg-white px-2 py-2 border-b">
                <input x-ref="searchInput" x-model="search" type="text" class="w-full border-gray-300 rounded-md p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900" placeholder="Cari...">
            </div>
            <ul class="pt-1">
                <template x-for="option in filteredOptions" :key="option.value">
                    <li @click="select(option.value)" class="text-gray-900 cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-600 hover:text-white" :class="{'bg-blue-100 text-blue-900': selected == option.value}">
                        <span x-text="option.label" class="block truncate" :class="{'font-semibold': selected == option.value}"></span>
                    </li>
                </template>
                <li x-show="filteredOptions.length === 0" class="text-gray-500 cursor-default select-none relative py-2 pl-3 pr-9 text-center text-sm">Tidak ditemukan.</li>
            </ul>
        </div>
    </div>
</div>