<div>
    {{-- Header --}}
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Bonus Settings" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search" placeholder="Cari Level..." />
                </div>
                <div>
                    {{-- Tombol Tambah --}}
                    <x-widget.button color="neutral" name="Add Level" action="openModal()"/>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table w-full mt-6 px-4 pb-4">
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-md border border-default">
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th>No</x-table.th>
                        <x-table.th>Level Generasi</x-table.th>
                        <x-table.th>Percentage (%)</x-table.th>
                        <x-table.th>Actions</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @forelse ($settings as $item)
                        <x-table.tr>
                            <x-table.td>{{ $settings->firstItem() + $loop->index }}</x-table.td>
                            
                            <x-table.td>
                                <span class="font-medium text-gray-900">Level {{ $item->level }}</span>
                            </x-table.td>
                            
                            <x-table.td>
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded border border-blue-400">
                                    {{ $item->percentage }}%
                                </span>
                            </x-table.td>

                            <x-table.td>
                                <div class="flex items-center gap-2">
                                    {{-- Gunakan Widget Icon Button Baru --}}
                                    <x-widget.button-icon type="edit" action="openModal({{ $item->id }})" />
                                    <x-widget.button-icon type="delete" action="confirmDelete({{ $item->id }})" />
                                </div>
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="5" class="text-center py-8">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p>Belum ada setting level bonus.</p>
                                </div>
                            </x-table.td>
                        </x-table.tr>
                    @endforelse
                </tbody>
            </x-table.table>
            
            @if ($settings->hasPages())
                <div class="p-4 border-t border-gray-200">
                    {{ $settings->links() }}
                </div>
            @endif
        </div>
    </div>

     @if($isOpen)
       <x-modal.form-modal 
            :formTitle="$setting_id ? 'Edit Level Bonus' : 'Tambah Level Baru'"  
            action="store()" 
            height="h-auto"
        >
            <div class="grid gap-4 grid-cols-1 py-4 px-2 md:py-6">
                
                {{-- Input Level --}}
                <div>
                    <x-modal.input 
                        name="level" 
                        label="Level Generasi" 
                        type="number" 
                        placeholder="Contoh: 1, 2, 3..." 
                        :disabled="$setting_id ? true : false" 
                    />
                    <span class="text-xs text-gray-500 mt-1">Level urutan kedalaman member (1 = Direct Sponsor).</span>
                </div>

                {{-- Input Persentase --}}
                <div>
                    <x-modal.input 
                        name="percentage" 
                        label="Persentase Bonus (%)" 
                        type="number" 
                        step="0.01" 
                        placeholder="Contoh: 10" 
                    />
                    <span class="text-xs text-gray-500 mt-1">Masukkan angka saja (tanpa tanda %).</span>
                </div>
            </div>
        </x-modal.form-modal>
    @endif
   
    <x-alerts.success/>
    <x-alerts.delete-confirmation/>

</div>