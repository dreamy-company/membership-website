@props([
    'formTitle',
    'action',
    'width' => 'max-w-2xl', 
    'submitLabel' => null 
])

@php
    $isStore = Str::contains($action, ['store', 'create', 'add', 'save']);
    $btnText = $submitLabel ?? ($isStore ? 'Simpan' : 'Update');
    $btnColor = $isStore ? 'neutral' : 'warning';
@endphp

@teleport('body')
    <div 
        x-data
        x-trap.noscroll="true"
        x-on:keydown.escape.window="$wire.closeModal()"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        
        {{-- BACKDROP (Latar Gelap) --}}
        {{-- Animasi Fade In/Out yang Smooth --}}
        <div 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            {{-- Hapus wire:click.self jika ingin modal statis --}}
            wire:click="closeModal"
        ></div>

        {{-- MODAL CONTENT --}}
        {{-- Animasi Scale Up + Fade In yang 'Bouncy' sedikit --}}
        <div 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full {{ $width }} flex flex-col bg-slate-50 rounded-xl shadow-2xl border border-default max-h-[90vh] overflow-hidden"
        >
            
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-default p-4 md:p-5 shrink-0 bg-white/50 backdrop-blur-md sticky top-0 z-10">
                <h3 class="text-black text-lg font-bold text-heading tracking-tight leading-none">
                    {{ $formTitle }}
                </h3>

                <button type="button"
                    class="bg-transparent hover:bg-slate-200/80 hover:text-gray-900 rounded-full p-2 inline-flex justify-center items-center transition-all duration-200 text-gray-500 hover:rotate-90"
                    wire:click="closeModal">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-4 md:p-6 overflow-y-auto grow custom-scrollbar">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 border-t border-default p-4 md:p-5 shrink-0 bg-slate-50 sticky bottom-0 z-10">
                <x-widget.button-outline 
                    color="stone" 
                    name="Batal" 
                    action="closeModal()" 
                    wire:loading.attr="disabled"
                />
                
                <div class="relative">
                    <x-widget.button 
                        color="{{ $btnColor }}" 
                        name="{{ $btnText }}" 
                        action="{{ $action }}"
                        class="min-w-[80px] transition-all active:scale-95" 
                    />
                    
                    <div wire:loading wire:target="{{ $action }}" class="absolute inset-0 flex items-center justify-center bg-white/60 rounded-md backdrop-blur-[1px]">
                        <svg class="animate-spin h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endteleport