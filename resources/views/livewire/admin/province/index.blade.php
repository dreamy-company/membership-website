<div>
    {{-- header --}}
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Provinces" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search" placeholder="Search Provinces" />
                </div>
                <div>
                    <x-widget.button color="neutral" name="Add Province" action="openModal()" />
                </div>
            </div>
        </div>
    </div>

    {{-- @dd($provinces) --}}

    {{-- table --}}
    <div class="table w-full mt-6 px-4 pb-4">
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-md border border-default">
            <table class="w-full text-sm text-left rtl:text-right text-body">
                <thead class="text-sm text-body bg-neutral-200 border-b rounded-base border-default">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">
                            No
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($provinces as $item)
                        <tr class="bg-neutral-primary border-b border-default">
                            <td class="px-6 py-4">
                                {{ $provinces->firstItem() + $loop->index }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $item->name }}
                            </td>
                            <td class="px-6 py-4">
                                <x-widget.button color="neutral" name="Edit" action="openModal({{ $item->id }})" />
                                <x-widget.button color="danger" name="Delete" action="confirmDelete({{ $item->id }})" />
                            </td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center">
                                No provinces found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">
                {{ $provinces->links() }}
            </div>
        </div>
    </div>

     <!-- Modal -->
    @if($isOpen)
    <div 
        id="crud-modal" 
        class="fixed inset-0 bg-black/30 z-50 flex justify-center items-center overflow-y-auto"
    >
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-slate-50 border border-default rounded-md shadow-sm p-4 md:p-6">

                <!-- Header -->
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        {{ $province_id ? 'Edit Provinsi' : 'Tambah Provinsi' }}
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
                <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                    <div class="col-span-2">
                        <label for="name" class="block mb-2.5 text-sm font-medium text-heading">Nama Provinsi</label>
                        <input 
                            type="text" 
                            wire:model.defer="name"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-md focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" 
                            placeholder="Contoh: Bali"
                        >
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">

                    <x-widget.button color="neutral" name="Send" action="store()" />
                    <x-widget.button-outline color="stone" name="Cancel" action="closeModal()" />
                </div>

            </div>
        </div>
    </div>
    @endif
   
    <script>
        window.addEventListener('swal:success', function (event) {
            Swal.fire({
                toast: true,
                icon: event.detail[0].type,
                title: event.detail[0].message,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
            });
        });

        window.addEventListener('show-delete-confirmation', event => {
            Swal.fire({
                title: 'Yakin hapus?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('delete')
                }
            });
        });
    </script>


</div>
