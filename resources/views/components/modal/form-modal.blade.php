@props([
    'formTitle',
    'action',
    'width' => 'max-w-2xl', // Bisa di-override jika butuh modal lebih lebar
    'submitLabel' => null // Opsional: jika ingin custom text tombol
])

@php
    // Logika otomatis menentukan teks & warna tombol
    // Cek apakah action mengandung kata 'store', 'create', atau 'add'
    $isStore = Str::contains($action, ['store', 'create', 'add', 'save']);
    
    $btnText = $submitLabel ?? ($isStore ? 'Simpan' : 'Update');
    $btnColor = $isStore ? 'neutral' : 'warning';
@endphp

@teleport('body')
    <div 
        x-data
        x-trap.noscroll="true"
        x-on:keydown.escape.window="$wire.closeModal()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 animate-in fade-in duration-200"
        wire:click.self="closeModal"
    >
        
        <div class="relative w-full {{ $width }} flex flex-col bg-slate-50 rounded-lg shadow-xl border border-default max-h-[90vh] animate-in zoom-in-95 duration-200">
            
            <div class="flex items-center justify-between border-b border-default p-4 md:p-5 shrink-0">
                <h3 class="text-black text-lg font-semibold text-heading tracking-tight">
                    {{ $formTitle }}
                </h3>

                <button type="button"
                    class="bg-transparent hover:bg-slate-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-colors text-black"
                    wire:click="closeModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <div class="p-4 md:p-6 overflow-y-auto grow">
                {{ $slot }}
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-default p-4 md:p-5 shrink-0 bg-slate-50/50 rounded-b-lg text-black">
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
                        class="min-w-[80px]" 
                    />
                    
                    <div wire:loading wire:target="{{ $action }}" class="absolute inset-0 flex items-center justify-center bg-white/50 rounded-md">
                        <svg class="animate-spin h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endteleport