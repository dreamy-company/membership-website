<div>
    {{-- header --}}
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Provinces" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                {{-- <div class="flex items-center mb-4 sm:mb-0">
                    <flux:input icon="magnifying-glass" wire:model.live.debounce.250ms="search"
                        placeholder="Search Members" />
                </div> --}}
            </div>
        </div>
    </div>

    <div class="py-4 md:py-6">
        {{-- FOTO PROFIL --}}
        <div class="flex justify-center mt-16 mb-4">
            <div class="flex justify-center -mt-16 mb-4">
                @if ($old_profile_picture)
                    <img src="{{ asset("storage/{$old_profile_picture}") }}" alt="Profile"
                        class="w-32 h-32 object-cover rounded-full shadow-lg border-4 border-white">
                @else
                    <div
                        class="w-32 h-32 rounded-full shadow-lg border-4 border-white bg-gray-300 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                @endif
            </div>
        </div>
        <div class="p-4 shadow-sm rounded-md bg-white">
            <h3 class="font-semibold mb-4 text-black">Profile Picture</h3>
            <div class="mb-2 text-sm text-gray-500">
                Ukuran maksimal file: 2 MB.
            </div>
            <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md p-4 cursor-pointer hover:border-gray-400 transition"
                onclick="document.getElementById('profile_picture_input').click()">
                {{-- Preview --}}
                <div class="mb-2 w-full flex justify-center">
                    @if ($profile_picture instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                        <img src="{{ $profile_picture->temporaryUrl() }}" alt="Preview"
                            class="max-h-40 rounded-md border border-gray-300">
                    @endif
                </div>

                <input type="file" id="profile_picture_input" wire:model="profile_picture" accept="image/*"
                    class="hidden" />
                <label class="text-center text-gray-600 text-sm mt-2">Choose File</label>

                <span class="text-gray-500 text-sm">
                    Drag & drop a file here or click to select
                </span>

                <div wire:loading wire:target="profile_picture" class="text-gray-500 text-sm mt-2">
                    Loading preview...
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-2 mb-4 border-b p-4 shadow-sm rounded-md bg-white mt-4">
            <div>
                <x-modal.input name="name" label="Name" type="text" placeholder="John Doe" required />
            </div>
            <div>
                <x-modal.input name="email" label="Email" type="email" placeholder="Contoh: user@example.com" />
            </div>
            {{-- PASSWORD FIELD --}}
            {{-- Tambahkan wire:key dan autocomplete --}}
            <div wire:key="pw-field-{{ $formKey }}">
                <x-modal.input name="password" label="Password" type="password" placeholder="********"
                    autocomplete="new-password" />
                <p class="text-sm text-gray-500 mt-1">Leave blank if you do not want to change the password.</p>
            </div>

            {{-- CONFIRM PASSWORD FIELD --}}
            {{-- Tambahkan wire:key dan autocomplete --}}
            <div wire:key="pw-confirm-field-{{ $formKey }}">
                <x-modal.input name="password_confirmation" label="Confirm Password" type="password"
                    placeholder="********" autocomplete="new-password" />
            </div>
        </div>
        <div class="border-b p-4 shadow-sm rounded-md mb-4 bg-white">
            <h3 class="font-semibold mb-4">Basic Information</h3>

            <div class="grid grid-cols-1 gap-2 mb-4">
                <div>
                    <x-modal.input name="nik" label="NIK" type="text" placeholder="Contoh: 1234567890" />
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
                    <x-modal.input name="bank_name" label="Bank Name" type="text" placeholder="Contoh: BCA" />
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
                    <x-modal.input name="npwp" label="NPWP" type="text" placeholder="Contoh: 1234567890" />
                </div>
            </div>
        </div>
        <div class="w-full mt-4 flex justify-end">
            <x-widget.button color="warning" name="Update" action="update({{ $member_id }})" />
        </div>

    </div>


    <x-alerts.success />
    <x-alerts.delete-confirmation />

</div>
