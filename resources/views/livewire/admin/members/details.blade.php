<div>
    {{-- Header --}}
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-dashboard.breadcrumbs title="Provinces" />
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-start gap-4 md:gap-0">
                <div>
                    <h1 class="text-xl font-semibold text-blue-600 sm:text-2xl dark:text-white">Total Members:
                        {{ $totalMembers ?? 0 }}</h1>
                </div>

                {{-- TOMBOL GANTI VIEW (3 OPSI) --}}
                <div class="flex bg-gray-100 p-1 rounded-lg border border-gray-200 gap-1">
                    {{-- 1. List View --}}
                    <button wire:click="switchView('list')"
                        class="px-3 py-2 text-xs cursor-pointer font-medium rounded-md transition-all {{ $viewType === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        List
                    </button>

                    {{-- 2. Chart View --}}
                    <button wire:click="switchView('chart')"
                        class="px-3 py-2 text-xs cursor-pointer font-medium rounded-md transition-all {{ $viewType === 'chart' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Chart
                    </button>

                    {{-- 3. Tree View (BARU) --}}
                    <button wire:click="switchView('tree')"
                        class="px-3 py-2 text-xs cursor-pointer font-medium rounded-md transition-all {{ $viewType === 'tree' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        <div class="flex items-center gap-1">
                            Tree
                        </div>
                    </button>
                </div>

                <div>
                    <x-widget.button color="neutral" name="Add Member" action="openModal()" />
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 bg-gray-50 min-h-[700px]">

        {{-- OPSI 1: LIST VIEW --}}
        @if ($viewType === 'list')
            <div class="space-y-4 h-[700px] overflow-auto w-full bg-white p-4 rounded-lg shadow-sm border">
                @forelse ($members as $member)
                    <x-tree-node-list :node="$member" />
                @empty
                    <p class="text-center py-4 text-gray-500">Tidak ada member.</p>
                @endforelse
            </div>
        @endif

        {{-- OPSI 2: CHART VIEW --}}
        @if ($viewType === 'chart')
            <div class="genealogy-scroll border rounded-lg bg-white h-[700px]">
                <div class="genealogy-tree">
                    <ul>
                        @forelse ($members as $member)
                            <x-tree-node-chart :node="$member" />
                        @empty
                            <li class="w-full text-center">
                                <p class="py-4 text-gray-500">Tidak ada member.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endif

        {{-- OPSI 3: TREE VIEW (BARU) --}}
        @if ($viewType === 'tree')
            <div class="h-[700px] overflow-auto w-full bg-white p-6 rounded-lg shadow-sm border">
                <ul class="tree-structure">
                    @forelse ($members as $member)
                        <x-tree-node-tree :node="$member" />
                    @empty
                        <li class="text-gray-500 italic">Tidak ada member.</li>
                    @endforelse
                </ul>
            </div>
        @endif

    </div>
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
                    </div>
                    <div>
                        <x-modal.input name="password_confirmation" label="Confirm Password" type="password" placeholder="********" />
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
                            <x-modal.input name="nik" label="NIK" type="number" placeholder="Contoh: 1234567890" />
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
                            :options="($parentOptions ?? collect([]))->map(fn($m) => [
                                    'value' => $m->user->id, 
                                    'label' => $m->member_code . ' - ' . $m->user->name
                            ])->values()->toArray()" 
                                />
                            </div>
                        @else
                            {{-- Pesan Feedback (Optional) --}}
                            <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                                <span class="font-medium">Info:</span> Member ini akan menjadi akar jaringan (Level 1).
                            </div>
                        @endif
                    </div>
                     {{-- <div class="flex items-center gap-2 border p-3 rounded-lg bg-gray-50 dark:bg-neutral-800 border-gray-200 dark:border-neutral-700">
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
                    <div>
                        <x-modal.searchable-select 
                            name="parent_user_id" 
                            label="Parent User" 
                            wire:model="parent_user_id"
                            :options="($parentOptions ?? collect([]))->map(fn($m) => [
                                'value' => $m->user->id, 
                                'label' => $m->member_code . ' - ' . $m->user->name
                            ])->values()->toArray()" 
                        />
                    </div> --}}
                </div>

                {{-- 3. BANK INFORMATION --}}
                <div class="border-b p-4 shadow-sm rounded-md mb-4 bg-white">
                    <h3 class="font-semibold mb-4 text-black">Bank Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
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
                        @if ($profile_picture)
                            <img src="{{ asset("storage/{$profile_picture}") }}" alt="Profile"
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
