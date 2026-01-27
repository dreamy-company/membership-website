@props([
    'name', 
    'label', 
    'options' => [], // Menerima array data
    'placeholder' => 'Select an option'
])

<div 
    x-data="{
        open: false,
        search: '',
        selected: @entangle($name), // Sinkron dengan wire:model parent
        options: {{ json_encode($options) }}, // Ambil data dari PHP
        
        // Logika untuk menampilkan teks label berdasarkan ID yang terpilih
        get selectedLabel() {
            if (!this.selected) return '';
            let found = this.options.find(o => o.value == this.selected);
            return found ? found.label : '';
        },

        // Logika filter pencarian
        get filteredOptions() {
            if (this.search === '') return this.options;
            return this.options.filter(option => {
                return option.label.toLowerCase().includes(this.search.toLowerCase());
            });
        },

        select(value) {
            this.selected = value;
            this.open = false;
            this.search = '';
        }
    }" 
    class="relative w-full mb-4"
>
    {{-- Label --}}
    <label class="block mb-2 text-sm font-medium text-black">
        {!! $label !!}
    </label>

    {{-- Trigger Button (Tampilan Input) --}}
    <div class="relative">
        <button 
            type="button" 
            @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
            class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-left shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-black flex justify-between items-center"
        >
            <span x-text="selected ? selectedLabel : '{{ $placeholder }}'" :class="{'text-gray-400': !selected}"></span>
            
            {{-- Icon Panah --}}
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        {{-- Dropdown Body --}}
        <div 
            x-show="open" 
            @click.away="open = false"
            class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
            style="display: none;"
        >
            {{-- Input Search --}}
            <div class="sticky top-0 z-10 bg-white px-2 py-2 border-b">
                <input 
                    x-ref="searchInput"
                    x-model="search"
                    type="text" 
                    class="w-full border-gray-300 rounded-md p-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-black" 
                    placeholder="Search..."
                >
            </div>

            {{-- List Options --}}
            <ul class="pt-1">
                <template x-for="option in filteredOptions" :key="option.value">
                    <li 
                        @click="select(option.value)"
                        class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-blue-600 hover:text-white cursor-pointer"
                        :class="{'bg-blue-100 text-blue-900': selected == option.value}"
                    >
                        <span x-text="option.label" class="block truncate" :class="{'font-semibold': selected == option.value, 'font-normal': selected != option.value}"></span>
                        
                        {{-- Centang Checkmark jika dipilih --}}
                        <span x-show="selected == option.value" class="absolute inset-y-0 right-0 flex items-center pr-4 text-blue-600" :class="{'text-white': false}">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </li>
                </template>
                
                {{-- State jika tidak ada hasil --}}
                <li x-show="filteredOptions.length === 0" class="text-gray-500 cursor-default select-none relative py-2 pl-3 pr-9 text-center">
                    No results found.
                </li>
            </ul>
        </div>
    </div>

    @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>