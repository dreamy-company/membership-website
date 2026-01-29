@props(['formTitle', 'action'])

{{-- 1. WRAPPER UTAMA --}}
{{-- 'z-50 fixed inset-0': Menutupi layar --}}
{{-- 'overflow-y-auto': Biar layar browser yang scroll kalau struk kepanjangan --}}
<div id="card-modal" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    {{-- 2. FLEX CONTAINER UNTUK POSISI --}}
    {{-- 'min-h-screen': Memastikan container minimal setinggi layar --}}
    {{-- 'flex items-center justify-center': Posisi tengah --}}
    {{-- 'p-4 md:p-8': MEMBERI JARAK (PADDING) ATAS BAWAH KANAN KIRI --}}
    <div class="flex min-h-screen items-center justify-center p-4 md:p-8 text-center sm:p-0">

        {{-- 3. BACKGROUND CLICK CLOSE (Opsional, biar klik gelap nutup) --}}
        <div class="fixed inset-0 transition-opacity cursor-pointer" aria-hidden="true" wire:click="closeModal()"></div>

        {{-- 4. KARTU MODAL --}}
        {{-- 'relative': Supaya tampil di atas background --}}
        {{-- 'my-8': Tambahan margin vertical biar makin aman --}}
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all w-full max-w-lg my-8 border border-gray-200">
            
            {{-- HEADER --}}
            @if(isset($formTitle))
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                        {{ $formTitle }}
                    </h3>
                    <button wire:click="closeModal()" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- BODY --}}
            <div class="p-6">
                {{ $slot }}
            </div>

        </div>
    </div>
</div>