<div>
    {{-- header --}}
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Members" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    {{-- <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search" placeholder="Search Members" /> --}}
                </div>
                <div>
                    <x-widget.button color="neutral" name="Add Member" action="openModal()" />
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
                        <x-table.th>No</x-table.th>
                        <x-table.th>Member Code</x-table.th>
                        <x-table.th>NIK</x-table.th>
                        <x-table.th>User</x-table.th>
                        <x-table.th>Parent Member</x-table.th>
                        <x-table.th>Phone</x-table.th>
                        <x-table.th>Gender</x-table.th>
                        <x-table.th>Address</x-table.th>
                        <x-table.th>Birth Date</x-table.th>
                        <x-table.th>NPWP</x-table.th>
                        <x-table.th>Province</x-table.th>
                        <x-table.th>Domicile</x-table.th>
                        <x-table.th>Bank Name</x-table.th>
                        <x-table.th>Account Number</x-table.th>
                        <x-table.th>Account Name</x-table.th>
                        <x-table.th>Profile Picture</x-table.th>
                        <x-table.th>Actions</x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <tbody>
                    @forelse ($members as $item)
                        <x-table.tr>
                            <x-table.td>{{ $members->firstItem() + $loop->index }}</x-table.td>
                            <x-table.td>{{ $item->member_code }}</x-table.td>
                            <x-table.td>{{ $item->nik }}</x-table.td>
                            <x-table.td>{{ $item->user->name }}</x-table.td>
                            <x-table.td>{{ $item->parentMember->user->name ?? '-' }}</x-table.td>
                            <x-table.td>{{ $item->phone_number }}</x-table.td>
                            <x-table.td>{{ $item->gender }}</x-table.td>
                            <x-table.td>{{ $item->address }}</x-table.td>
                            <x-table.td>{{ $item->birth_date }}</x-table.td>
                            <x-table.td>{{ $item->npwp }}</x-table.td>
                            <x-table.td>{{ $item->province->name ?? '-' }}</x-table.td>
                            <x-table.td>{{ $item->domicile->name ?? '-' }}</x-table.td>
                            <x-table.td>{{ $item->bank_name }}</x-table.td>
                            <x-table.td>{{ $item->account_number }}</x-table.td>
                            <x-table.td>{{ $item->account_name }}</x-table.td>
                            <x-table.td>{{ $item->profile_picture }}</x-table.td>
                            <x-table.td>
                                <x-widget.button color="neutral" name="Edit" action="openModal({{ $item->id }})" />
                                <x-widget.button color="danger" name="Delete" action="confirmDelete({{ $item->id }})" />
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="5" class="text-center py-4">
                                No Members found.
                            </x-table.td>
                        </x-table.tr>
                    @endforelse
                </tbody>
            </x-table.table>
           @if ($members->hasPages())
                <div class="p-4">
                    {{ $members->links() }}
                </div>
            @endif
        </div>
    </div>

     <!-- Modal -->
    @if($isOpen)
        <x-modal.form-modal :formTitle="$member_id ? 'Edit Member' : 'Add Member'" action="store()">
            <div class="py-4 md:py-6">

                {{-- User & Parent --}}
                <div class="grid grid-cols-2 gap-2 mb-4 border-b p-4 shadow-sm rounded-md">
                    <div>
                        <x-modal.select name="user_id" label="User">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </x-modal.select>
                    </div>
                    <div>
                        <x-modal.select name="parent_member_id" label="Parent Member">
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->user->name }}</option>
                            @endforeach
                        </x-modal.select>
                    </div>
                </div>

                {{-- Basic Info --}}
                <div class="border-b p-4 shadow-sm rounded-md mb-4">

                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div>
                            <x-modal.input name="member_code" label="Member Code" type="text" placeholder="Contoh: MBR0001" />
                        </div>
                        <div>
                            <x-modal.input name="nik" label="NIK" type="text" placeholder="Contoh: 1234567890" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div>
                            <x-modal.select name="gender" label="Gender">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </x-modal.select>
                        </div>
                        <div>
                            <x-modal.input name="phone_number" label="Phone" type="number" placeholder="Contoh: 081234567890" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div>
                            <x-modal.input name="address" label="Address" type="text" placeholder="Contoh: Jalan Merdeka No. 123" />
                        </div>
                        <div>
                            <x-modal.input name="birth_date" label="Birth Date" type="date" placeholder="Contoh: 1990-01-01" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div>
                            <x-modal.select name="province_id" label="Province">
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </x-modal.select>
                        </div>
                        <div>
                            <x-modal.select name="domicile_id" label="Domicile">
                                @foreach($domicilies as $domicile)
                                    <option value="{{ $domicile->id }}">{{ $domicile->name }}</option>
                                @endforeach
                            </x-modal.select>
                        </div>
                    </div>

                </div>

                {{-- Bank Info --}}
                <div class="grid grid-cols-2 gap-2 mb-4 p-4 shadow-sm rounded-md">
                    <div>
                        <x-modal.input name="bank_name" label="Bank Name" type="text" placeholder="Contoh: BCA" />
                    </div>
                    <div>
                        <x-modal.input name="account_number" label="Account Number" type="text" placeholder="Contoh: 1234567890" />
                    </div>
                    <div>
                        <x-modal.input name="account_name" label="Account Name" type="text" placeholder="Contoh: John Doe" />
                    </div>
                    <div>
                        <x-modal.input name="npwp" label="NPWP" type="text" placeholder="Contoh: 1234567890" />
                    </div>
                </div>

                {{-- profile picture --}}
                <div class="grid grid-cols-1 gap-2 mb-4">
                    <label class="block mb-2.5 text-sm font-medium text-heading">Payment Receipt</label>
                    <div 
                        class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md p-4 cursor-pointer hover:border-gray-400 transition"
                        x-data
                        @dragover.prevent="dragging=true"
                        @dragleave.prevent="dragging=false"
                        @drop.prevent="$refs.fileInput.files = $event.dataTransfer.files; $dispatch('input', $event.dataTransfer.files)"
                    >
                        <input 
                            type="file" 
                            wire:model="profile_picture" 
                            class="hidden" 
                            x-ref="fileInput"
                        />

                        {{-- Preview --}}
                        <div class="mb-2 w-full flex justify-center">
                            @if ($profile_picture instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                <img src="{{ $profile_picture->temporaryUrl() }}" alt="Preview" class="max-h-40 rounded-md border border-gray-300">
                            @elseif(!empty($old_profile_picture))
                                <img src="{{ asset('storage/'.$old_profile_picture) }}" alt="Old Image" class="max-h-40 rounded-md border border-gray-300">
                            @endif
                        </div>

                        <span class="text-gray-500 text-sm">
                            Drag & drop a file here or click to select
                        </span>
                        <button type="button" class="mt-2 px-3 py-1 bg-gray-200 rounded" @click="$refs.fileInput.click()">
                            Select File
                        </button>

                        <div wire:loading wire:target="payment_receipt" class="text-gray-500 text-sm mt-2">
                            Loading preview...
                        </div>
                    </div>
                </div>

            </div>
        </x-modal.form-modal>

    @endif
   
    <x-alerts.success/>
    <x-alerts.delete-confirmation/>



</div>
