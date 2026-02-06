<div>
    {{-- header --}}
    <div class="p-4 bg-gray-50/50"> {{-- Background agak abu dikit biar container putih pop-up --}}
        
        <div class="mx-auto">
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <x-dashboard.breadcrumbs title="Members" />
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Members Data</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage and filter your organization members.</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <x-widget.button color="neutral" name="Add Member" action="openModal()" />
                    <x-widget.button color="neutral" name="Tree View" action="redirectToMemberDetails()" />
                </div>
            </div>

            {{-- MODERN SEARCH BAR / TOOLBAR --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-1.5">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                    
                    {{-- 1. Search Name (Primary Search - Lebih Lebar) --}}
                    <div class="md:col-span-3 relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400 group-focus-within:text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A7.5 7.5 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.500ms="searchName" 
                            class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-0 rounded-lg text-gray-900 text-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-all" 
                            placeholder="Search by Name...">
                    </div>

                    {{-- 2. Member Code --}}
                    <div class="md:col-span-2 relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400 group-focus-within:text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5-3.9 19.5" /></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.500ms="searchCode" 
                            class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-0 rounded-lg text-gray-900 text-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-all" 
                            placeholder="ID Code">
                    </div>

                    {{-- 3. Gender (Select) --}}
                    <div class="md:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A7.5 7.5 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                        </div>
                        <select wire:model.live="searchGender" 
                            class="block w-full pl-10 pr-8 py-2.5 bg-gray-50 border-0 rounded-lg text-gray-900 text-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-all appearance-none cursor-pointer">
                            <option value="">Gender: All</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        {{-- Chevron Icon custom --}}
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>

                    {{-- 4. Address --}}
                    <div class="md:col-span-2 relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400 group-focus-within:text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.500ms="searchAddress" 
                            class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-0 rounded-lg text-gray-900 text-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-all" 
                            placeholder="Address">
                    </div>

                    {{-- 5. Bank --}}
                    <div class="md:col-span-2 relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400 group-focus-within:text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.500ms="searchBank" 
                            class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-0 rounded-lg text-gray-900 text-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-all" 
                            placeholder="Bank info">
                    </div>

                    {{-- Reset Button (Icon Only) --}}
                    <div class="md:col-span-1 flex items-center justify-center">
                        <button wire:click="resetFilters" title="Reset Filters"
                            class="p-2.5 w-full text-gray-500 bg-white hover:bg-red-50 hover:text-red-600 rounded-lg border border-dashed border-gray-300 hover:border-red-300 transition-all flex items-center justify-center gap-2 group">
                            <svg class="w-5 h-5 transition-transform group-hover:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- @dd($provinces) --}}

    {{-- table --}}
    <div class="w-full mt-6 px-4 pb-4">
        <div class="overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-md border border-default">
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th>No</x-table.th>
                        <x-table.th>Member Code</x-table.th>
                        <x-table.th>User</x-table.th>
                        <x-table.th>Email</x-table.th>
                        <x-table.th>Phone</x-table.th>
                        <x-table.th>Gender</x-table.th>
                        <x-table.th>Address</x-table.th>
                        <x-table.th>Bank Name</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Actions</x-table.th>
                    </x-table.tr>
                </x-table.thead>
                
                <tbody>
                    @forelse ($members as $item)
                        <x-table.tr>
                            <x-table.td>{{ $members->firstItem() + $loop->index }}</x-table.td>
                            <x-table.td>{{ $item->member_code }}</x-table.td>
                            <x-table.td>{{ $item->user->name }}</x-table.td>
                            <x-table.td>{{ $item->user->email }}</x-table.td>
                            <x-table.td>{{ $item->phone_number }}</x-table.td>
                            <x-table.td>{{ $item->gender }}</x-table.td>
                            <x-table.td>{{ $item->address }}</x-table.td>
                            <x-table.td>{{ $item->bank_name }}</x-table.td>
                            <x-table.td>{{ $item->status }}</x-table.td>
                            <x-table.td>
                                <div class="flex gap-2">
                                    <x-widget.button-icon type="detail" color='detail' action="openCardModal({{ $item->id }})" />
                                    <x-widget.button-icon type="edit" action="openModal({{ $item->id }})" />
                                    {{-- <x-widget.button-icon type="delete" action="confirmDelete({{ $item->id }})" /> --}}
                                </div>
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="10" class="text-center py-4">
                                No Members found.
                            </x-table.td>
                        </x-table.tr>
                    @endforelse
                </tbody>
            </x-table.table>
        </div>
    </div>
    @if ($members->hasPages())
         <div class="p-4">
             {{ $members->links() }}
         </div>
     @endif

     @if ($isOpen)
        <x-modal.form-modal :formTitle="$member_id ? 'Edit Member' : 'Add Member'" action="store()">
            <div class="py-4 md:py-6">

                {{-- 1. USER ACCOUNT --}}
                <div class="grid grid-cols-1 gap-2 mb-4 border-b p-4 shadow-sm rounded-md bg-white">
                    <h3 class="font-semibold mb-4 text-black">Account Information</h3>
                    <div>
                        <x-modal.input name="name" label="Name" type="text" placeholder="John Doe" required />
                    </div>
                    <div>
                        <x-modal.input name="email" label="Email" type="email" placeholder="Contoh: user@example.com" />
                    </div>
                    <div>
                        <x-modal.input name="password" label="Password" type="password" placeholder="********" />
                        
                        {{-- Container Info --}}
                        <div class="mt-1.5 flex items-start gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                            {{-- Icon Info (SVG) --}}
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>

                            {{-- Teks Dinamis --}}
                            <div class="leading-relaxed">
                                @if($member_id)
                                    {{-- PESAN SAAT EDIT --}}
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Edit Mode:</span> 
                                    Biarkan kosong jika tidak ingin mengubah password. 
                                    <span class="block text-gray-400 mt-0.5">(Min. 8 karakter jika diisi)</span>
                                @else
                                    {{-- PESAN SAAT CREATE --}}
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Wajib Diisi:</span> 
                                    Minimal 8 karakter kombinasi huruf & angka.
                                @endif
                            </div>
                        </div>
                    </div>
                    <div>
                        <x-modal.input name="password_confirmation" label="Confirm Password" type="password" placeholder="********" />
                        {{-- Container Info --}}
                        <div class="mt-1.5 flex items-start gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                            {{-- Icon Info (SVG) --}}
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>

                            {{-- Teks Dinamis --}}
                            <div class="leading-relaxed">
                                @if($member_id)
                                    {{-- PESAN SAAT EDIT --}}
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Edit Mode:</span> 
                                    Biarkan kosong jika tidak ingin mengubah password. 
                                    <span class="block text-gray-400 mt-0.5">(Min. 8 karakter jika diisi)</span>
                                @else
                                    {{-- PESAN SAAT CREATE --}}
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Wajib Diisi:</span> 
                                    Minimal 8 karakter kombinasi huruf & angka.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. BASIC INFORMATION --}}
                <div class="border-b p-4 shadow-sm rounded-md mb-4 bg-white">
                    <h3 class="font-semibold mb-4 text-black">Basic Information</h3>

                    {{-- [BARU] Input Status --}}
                    <div class="grid grid-cols-1 gap-2 mb-4 {{ $member_id ? '' : 'hidden' }}">
                        <x-modal.select name="status" label="Status Member" wire:model="status">
                            <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </x-modal.select>
                    </div>

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
                            <x-modal.input name="phone_number" label="Phone" type="number" placeholder="Contoh: 081234567890" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                        <div>
                            <x-modal.input name="address" label="Address" type="text" placeholder="Contoh: Jalan Merdeka No. 123" />
                        </div>
                        <div>
                            <x-modal.input name="birth_date" label="Birth Date" type="date" placeholder="Contoh: 1990-01-01" />
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

                   <div class="grid grid-cols-1 gap-4 mb-4">
    
                    {{-- CHECKBOX UI --}}
                    <div class="flex items-center gap-2 border p-3 rounded-lg bg-gray-50 dark:bg-neutral-800 border-gray-200 dark:border-neutral-700">
                        <input 
                            type="checkbox" 
                            id="is_root" 
                            wire:model.live="is_root" 
                            class="w-4 h-4 text-black bg-gray-100 border-gray-300 rounded focus:ring-black dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                        >
                        <label for="is_root" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                            Jadikan Top Leader (Tanpa Upline/Parent)
                        </label>
                    </div>

                    {{-- DROPDOWN (Hanya muncul jika TIDAK dicentang) --}}
                    @if(!$is_root)
                        <div class="transition-all duration-300 ease-in-out">
                            <x-modal.searchable-select 
                            name="parent_user_id" 
                            label="Parent User" 
                            wire:model="parent_user_id"
                            :options="$members->map(fn($m) => ['value' => $m->user->id, 'label' => $m->member_code . ' - ' . $m->user->name])" 
                            />
                        </div>
                    @else
                        {{-- Pesan Feedback (Optional) --}}
                        <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                            <span class="font-medium">Info:</span> Member ini akan menjadi akar jaringan (Level 1).
                        </div>
                    @endif

                </div>
                </div>

                {{-- 3. BANK INFORMATION --}}
                <div class="border-b p-4 shadow-sm rounded-md mb-4 bg-white">
                    <h3 class="font-semibold mb-4 text-black">Bank Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <x-modal.input name="bank_name" label="Bank Name" type="text" placeholder="Contoh: BCA" />
                        </div>
                        <div>
                            <x-modal.input name="account_number" label="Account Number" type="number" placeholder="Contoh: 1234567890" />
                        </div>
                        <div>
                            <x-modal.input name="account_name" label="Account Name" type="text" placeholder="Contoh: John Doe" />
                        </div>
                        <div>
                            <x-modal.input name="npwp" label="NPWP" type="number" placeholder="Contoh: 1234567890" />
                        </div>
                    </div>
                </div>

                {{-- 4. PROFILE PICTURE (TANPA PREVIEW) --}}
                <div class="p-4 shadow-sm rounded-md bg-white">
                    <h3 class="font-semibold mb-2 text-black">Profile Picture</h3>
                    
                    <div class="mb-2">
                        <input 
                            type="file" 
                            wire:model="profile_picture" 
                            accept="image/*"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:border-blue-500 focus:ring-blue-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-l-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-neutral-900 file:text-white
                                hover:file:bg-neutral-700"
                        />
                    </div>

                    {{-- Loading State --}}
                    <div wire:loading wire:target="profile_picture">
                        <span class="text-xs text-blue-600 font-medium animate-pulse">
                            Mengupload gambar...
                        </span>
                    </div>
                    
                    @error('profile_picture')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Global Errors --}}
                @if($errors->any())
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
                        @foreach ($errors->all() as $error)
                            <div class="text-red-500 text-sm list-disc ml-4">{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

            </div>
        </x-modal.form-modal>
    @endif

    {{-- card --}}
    @if ($isCardOpen)

        <x-modal.card-modal :formTitle="'Detail'" action="store()">
            <div class="py-4 md:py-6">

                {{-- PROFILE CARD --}}
                <div
                    class="max-w-2xl mx-auto bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg overflow-hidden max-h-[700px]">

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

    {{-- withdrawal id --}}
    @if ($openWithdrawalModal)
        <x-modal.form-modal :formTitle="'Withdrawal'" action="processWithdrawal()" :height="'h-auto'">
            <div class="py-4 px-2 md:py-6">
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div>
                        <x-modal.input name="member_name" label="Member Name" type="text" :disabled="true"/>
                    </div>
                    <div>
                        <x-modal.input name="bonus" label="Bonus" type="number" :disabled="true"/>
                    </div>
                </div>
                <div>
                    <x-modal.input name="withdrawal_amount" label="Withdrawal Amount" type="number"/>
                </div>
                <div class="mb-0">
                    <label class="block mb-2.5 text-sm font-medium text-heading" for="file_input">Upload file</label>
                    <input name="payment_receipt" id="file" wire:model="payment_receipt" class="cursor-pointer bg-slate-50 border border-stone-500 text-heading text-sm rounded-md focus:ring-stone focus:border-stone block w-full shadow-xs placeholder:text-body p-2" type="file">
                    @error('payment_receipt')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-modal.form-modal>
    @endif

    <x-alerts.success/>
    <x-alerts.error/>
    <x-alerts.delete-confirmation/>



</div>
