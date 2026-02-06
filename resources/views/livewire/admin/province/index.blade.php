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
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th>ID</x-table.th>
                        <x-table.th>Code</x-table.th>
                        <x-table.th>Nama</x-table.th>
                        <x-table.th>Aksi</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @foreach ($provinces as $province)
                        <x-table.tr>
                            <x-table.td>{{ $loop->iteration }}</x-table.td>
                            <x-table.td>{{ $province->code }}</x-table.td>
                            <x-table.td>{{ $province->name }}</x-table.td>
                            <x-table.td>
                                <x-widget.button-icon type="edit" action="openModal({{ $province->id }})" />
                                <x-widget.button-icon type="delete" action="confirmDelete({{ $province->id }})" />
                            </x-table.td>
                        </x-table.tr>
                    @endforeach
                </tbody>
            </x-table.table>
            <div class="p-4">
                {{ $provinces->links() }}
            </div>
        </div>
    </div>

     <!-- Modal -->
    @if($isOpen)
        <x-modal.form-modal :formTitle="$province_id ? 'Edit Province' : 'Add Province'" action="store()" height="h-auto">
           <div class="grid gap-4 grid-cols-2 px-2 py-4 md:py-6">
                    <div class="col-span-2">
                        <x-modal.input name="code" label="Kode Provinsi" placeholder="Contoh: BA" />
                    </div>
                <div class="col-span-2">
                    <x-modal.input name="name" label="Nama Provinsi" placeholder="Contoh: Bali" />
                </div>
            </div>
        </x-modal.form-modal>
    @endif
   
    <x-alerts.success/>
    <x-alerts.delete-confirmation/>



</div>
