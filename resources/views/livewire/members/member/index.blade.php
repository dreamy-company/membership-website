<div>
    {{-- header --}}
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Provinces" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-blue-600 sm:text-2xl dark:text-white">Total Members:
                    {{ $totalMembers ?? 0 }}</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                {{-- <div class="flex items-center mb-4 sm:mb-0">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search"
                        placeholder="Search Members" />
                </div> --}}
                <div>
                    <x-widget.button color="neutral" name="Add Member" action="openModal()" />
                </div>
            </div>
        </div>
    </div>

    {{-- @dd($members) --}}

    {{-- Tree List --}}
    <div class="p-6">
        <div class="space-y-4 h-[700px] overflow-auto w-full">
            @forelse ($members as $member)
                <x-tree-node :node="$member" />
            @empty
                <p class="text-center py-4 text-gray-500">Tidak ada member ditemukan.</p>
            @endforelse
        </div>
    </div>

    <!-- Modal -->
    @if ($isOpen)
        <x-modal.form-modal :formTitle="$member_id ? 'Edit Member | ' . $name : 'Add Member ' . ' | On ' . $parentName" :action="$member_id ? 'update(' . $member_id . ')' : 'store()'">
            <div class="py-4 md:py-6">

                <div class="grid grid-cols-1 gap-2 mb-4 border-b p-4 shadow-sm rounded-md bg-white">
                    <div>
                        <x-modal.input name="name" label="Name" type="text" placeholder="John Doe" required />
                    </div>
                    <div>
                        <x-modal.input name="email" label="Email" type="email"
                            placeholder="Contoh: user@example.com" />
                    </div>
                    <div>
                        <x-modal.input name="password" label="Password" type="password" placeholder="********" />
                        @if ($member_id)
                            <p class="text-sm text-gray-500 mt-1">Leave blank if you do not want to change the password.
                            </p>
                        @endif
                    </div>
                    <div>
                        <x-modal.input name="password_confirmation" label="Confirm Password" type="password"
                            placeholder="********" />
                    </div>
                </div>
                <div class="border-b p-4 shadow-sm rounded-md mb-4 bg-white">
                    <h3 class="font-semibold mb-4">Basic Information</h3>

                    <div class="grid grid-cols-1 gap-2 mb-4">
                        <div>
                            <x-modal.input name="nik" label="NIK" type="text"
                                placeholder="Contoh: 1234567890" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                        <div>
                            <x-modal.select name="gender" label="Gender">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </x-modal.select>
                        </div>
                        <div>
                            <x-modal.input name="phone_number" label="Phone" type="number"
                                placeholder="Contoh: 081234567890" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                        <div>
                            <x-modal.input name="address" label="Address" type="text"
                                placeholder="Contoh: Jalan Merdeka No. 123" />
                        </div>
                        <div>
                            <x-modal.input name="birth_date" label="Birth Date" type="date"
                                placeholder="Contoh: 1990-01-01" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                        <div>
                            <x-modal.select name="province_id" label="Province">
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </x-modal.select>
                        </div>
                        <div>
                            <x-modal.select name="domicile_id" label="Domicile">
                                @foreach ($domicilies as $domicile)
                                    <option value="{{ $domicile->id }}">{{ $domicile->name }}</option>
                                @endforeach
                            </x-modal.select>
                        </div>
                    </div>
                </div>
                <div class="border-b p-4 shadow-sm rounded-md mb-4 bg-white">
                    <h3 class="font-semibold mb-4">Bank Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <x-modal.input name="bank_name" label="Bank Name" type="text"
                                placeholder="Contoh: BCA" />
                        </div>
                        <div>
                            <x-modal.input name="account_number" label="Account Number" type="text"
                                placeholder="Contoh: 1234567890" />
                        </div>
                        <div>
                            <x-modal.input name="account_name" label="Account Name" type="text"
                                placeholder="Contoh: John Doe" />
                        </div>
                        <div>
                            <x-modal.input name="npwp" label="NPWP" type="text"
                                placeholder="Contoh: 1234567890" />
                        </div>
                    </div>
                </div>
                <div class="p-4 shadow-sm rounded-md bg-white">
                    <h3 class="font-semibold mb-4">Profile Picture</h3>
                    <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md p-4 cursor-pointer hover:border-gray-400 transition"
                        x-data @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                        @drop.prevent="$refs.fileInput.files = $event.dataTransfer.files; $dispatch('input', $event.dataTransfer.files)">
                        <input type="file" wire:model="profile_picture" class="hidden" x-ref="fileInput" />

                        {{-- Preview --}}
                        <div class="mb-2 w-full flex justify-center">
                            @if ($profile_picture instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                <img src="{{ $profile_picture->temporaryUrl() }}" alt="Preview"
                                    class="max-h-40 rounded-md border border-gray-300">
                            @elseif(!empty($old_profile_picture))
                                <img src="{{ asset('storage/' . $old_profile_picture) }}" alt="Old Image"
                                    class="max-h-40 rounded-md border border-gray-300">
                            @endif
                        </div>

                        <span class="text-gray-500 text-sm">
                            Drag & drop a file here or click to select
                        </span>
                        <button type="button" class="mt-2 px-3 py-1 bg-gray-200 rounded"
                            @click="$refs.fileInput.click()">
                            Select File
                        </button>

                        <div wire:loading wire:target="profile_picture" class="text-gray-500 text-sm mt-2">
                            Loading preview...
                        </div>
                    </div>
                </div>

            </div>
        </x-modal.form-modal>
    @endif



    @if ($isCardOpen)

        <x-modal.card-modal :formTitle="'Detail'" action="store()">
            <div class="py-4 md:py-6">

                {{-- PROFILE CARD --}}
                <div
                    class="max-w-2xl mx-auto bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg overflow-hidden max-h-[700px] overflow-y-auto">

                    {{-- HEADER BACKGROUND --}}
                    <div class="h-32 bg-gradient-to-r from-blue-500 to-blue-600"></div>
                    {{-- FOTO PROFIL --}}
                    <div class="flex justify-center -mt-16 mb-4">
                        @if($profile_picture)
                            <img src="{{ asset("storage/{$profile_picture}") }}" alt="Profile"
                                class="w-32 h-32 object-cover rounded-full shadow-lg border-4 border-white">
                        @else
                            <div class="w-32 h-32 rounded-full shadow-lg border-4 border-white bg-gray-300 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- NAMA & EMAIL --}}
                    <div class="text-center mb-6 px-4">
                        <h2 class="text-3xl font-bold text-gray-900">{{ $name }}</h2>
                        <p class="text-blue-600 font-medium mt-1">{{ $email }}</p>
                    </div>

                    <div class="px-6 pb-6 space-y-4">

                        {{-- PERSONAL INFO SECTION --}}
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <h3 class="font-semibold text-gray-900 mb-3 text-sm uppercase tracking-wider">Personal
                                Information</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500 font-medium">NIK</p>
                                    <p class="text-gray-900 font-semibold">{{ $nik }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 font-medium">Phone</p>
                                    <p class="text-gray-900 font-semibold">{{ $phone_number }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 font-medium">Gender</p>
                                    <p class="text-gray-900 font-semibold capitalize">{{ $gender }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 font-medium">Birth Date</p>
                                    <p class="text-gray-900 font-semibold">{{ $birth_date }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- ADDRESS SECTION --}}
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <h3 class="font-semibold text-gray-900 mb-3 text-sm uppercase tracking-wider">Address</h3>
                            <div class="space-y-2 text-sm">
                                <p class="text-gray-700">{{ $address }}</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-gray-500 font-medium">Province</p>
                                        <p class="text-gray-900">{{ optional($province)->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 font-medium">Domicile</p>
                                        <p class="text-gray-900">{{ optional($domicile)->name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- BANK INFO SECTION --}}
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <h3 class="font-semibold text-gray-900 mb-3 text-sm uppercase tracking-wider">Bank
                                Information</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500 font-medium">Bank</p>
                                    <p class="text-gray-900 font-semibold">{{ $bank_name }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 font-medium">Account No</p>
                                    <p class="text-gray-900 font-semibold">{{ $account_number }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-gray-500 font-medium">Account Name</p>
                                    <p class="text-gray-900 font-semibold">{{ $account_name }}</p>
                                </div>
                                @if ($npwp)
                                    <div class="col-span-2">
                                        <p class="text-gray-500 font-medium">NPWP</p>
                                        <p class="text-gray-900 font-semibold">{{ $npwp }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                {{-- CLOSE BUTTON --}}
                <div class="flex justify-center mt-6">
                    <x-widget.button color="danger" name="Close" action="closeCardModal()" />
                </div>

            </div>
        </x-modal.card-modal>
    @endif


    <x-alerts.success />
    <x-alerts.delete-confirmation />

</div>
