@props(['formTitle', 'action'])

<div 
    id="crud-modal" 
    class="fixed inset-0 bg-black/30 z-50 flex justify-center items-center overflow-y-auto"
    >
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-slate-50 border border-default rounded-md shadow-sm p-4 md:p-6">

                <!-- Header -->
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        {{ $formTitle }}
                    </h3>

                    <button 
                        type="button" 
                        class="text-body bg-transparent hover:bg-slate-100 hover:text-heading rounded-md text-sm w-9 h-9 inline-flex justify-center items-center"
                        wire:click="closeModal"
                    >
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
               {{ $slot }}

                <!-- Footer -->
                <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                    <x-widget.button color="neutral" name="Send" action="store()" />
                    <x-widget.button-outline color="stone" name="Cancel" action="closeModal()" />
                </div>

            </div>
        </div>
    </div>