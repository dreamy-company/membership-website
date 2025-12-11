<?php

namespace App\Livewire\Admin\Members;

use App\Models\User;
use App\Models\Member;
use Livewire\Component;
use App\Models\Province;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $province;
    public $domicile;

    public $member_id;
    public $member_code;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $nik;
    public $user_id;
    public $parent_member_id;
    public $phone_number;
    public $gender;
    public $address;
    public $birth_date;
    public $npwp;
    public $province_id;
    public $domicile_id;
    public $bank_name;
    public $account_number;
    public $account_name;
    public $profile_picture; // untuk upload file
    public $old_profile_picture; // untuk preview saat edit

    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Member";

    public $users;
    public $provinces;
    public $domicilies;
    public $isCardOpen = false;

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->users = \App\Models\User::all();
        $this->provinces = \App\Models\Province::all();
        $this->domicilies = \App\Models\Domicile::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $members = Member::search($this->search)
                    ->latest()
                    ->paginate($this->perPage);

        return view('livewire.admin.members.index', compact('members'));
    }

    public function openModal($id = null)
    {
        $this->resetInput();

        if ($id) {
            $member = Member::findOrFail($id);

            $this->member_id = $member->id;
            $this->member_code = $member->member_code;
            $this->name = $member->user->name;
            $this->email = $member->user->email;
            $this->nik = $member->nik;
            $this->user_id = $member->user_id;
            $this->parent_member_id = $member->parent_member_id;
            $this->phone_number = $member->phone_number;
            $this->gender = $member->gender;
            $this->address = $member->address;
            $this->birth_date = $member->birth_date;
            $this->npwp = $member->npwp;
            $this->province_id = $member->province_id;
            $this->domicile_id = $member->domicile_id;
            $this->bank_name = $member->bank_name;
            $this->account_number = $member->account_number;
            $this->account_name = $member->account_name;
            $this->old_profile_picture = $member->profile_picture;
        }

        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function closeCardModal()
    {
        $this->isCardOpen = false;
    }
    
    

    private function resetInput()
    {
        $this->member_code = '';
        $this->nik = '';
        $this->user_id = '';
        $this->parent_member_id = '';
        $this->phone_number = '';
        $this->gender = '';
        $this->address = '';
        $this->birth_date = '';
        $this->npwp = '';
        $this->province_id = '';
        $this->domicile_id = '';
        $this->bank_name = '';
        $this->account_number = '';
        $this->account_name = '';
        $this->profile_picture = null;
        $this->old_profile_picture = null;
        $this->member_id = null;
    }

    public function store()
    {
        $this->validate();

        // ==========================
        // HANDLE USER
        // ==========================
        $user = User::updateOrCreate(
            ['id' => $this->user_id], // kalau ada → update, kalau engga → create
            [
                'name'  => $this->name,
                'email' => $this->email,
                'password' => $this->password 
                    ? bcrypt($this->password) 
                    : User::find($this->user_id)?->password,
            ]
        );

        // ==========================
        // HANDLE PROFILE PICTURE
        // ==========================
        $filename = $this->old_profile_picture;

        if ($this->profile_picture instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {

            if ($this->old_profile_picture && Storage::disk('public')->exists($this->old_profile_picture)) {
                Storage::disk('public')->delete($this->old_profile_picture);
            }

            $filename = $this->profile_picture->store('members', 'public');
        }

        // ==========================
        // GENERATE MEMBER CODE (only for create)
        // ==========================
        $province = Province::find($this->province_id);

        $member_code = $this->member_id
            ? Member::find($this->member_id)->member_code // keep old if update
            : $province->code . '-' . str_pad(Member::max('id') + 1, 4, '0', STR_PAD_LEFT);

        
        // ==========================
        // HANDLE MEMBER
        // ==========================
        Member::updateOrCreate(
            ['id' => $this->member_id],  // key
            [
                'member_code'      => $member_code,
                'nik'              => $this->nik,
                'user_id'          => $user->id,
                'parent_member_id' => $this->parent_member_id ?: null,
                'phone_number'     => $this->phone_number,
                'gender'           => $this->gender,
                'address'          => $this->address,
                'birth_date'       => $this->birth_date,
                'province_id'      => $this->province_id,
                'domicile_id'      => $this->domicile_id,
                'bank_name'        => $this->bank_name,
                'account_number'   => $this->account_number,
                'account_name'     => $this->account_name,
                'npwp'             => $this->npwp,
                'profile_picture'  => $filename,
            ]
        );

        $this->afterSave(!$this->member_id);
    }



    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        $member = Member::findOrFail($this->confirmingDelete);

        if ($member->profile_picture && Storage::disk('public')->exists($member->profile_picture)) {
            Storage::disk('public')->delete($member->profile_picture);
        }

        $member->delete();

        $this->dispatch('success', [
            'type' => 'success',
            'message' => 'Member berhasil dihapus!',
        ]);
    }

    public function openCardModal($memberId)
    {
        $member = Member::with(['province', 'domicile'])->findOrFail($memberId);

        // Isi semua variabel
        $this->name           = $member->name;
        $this->email          = $member->email;
        $this->nik            = $member->nik;
        $this->phone_number   = $member->phone_number;
        $this->gender         = $member->gender;
        $this->address        = $member->address;
        $this->birth_date     = $member->birth_date;
        $this->npwp           = $member->npwp;
        $this->province_id    = $member->province_id;
        $this->domicile_id    = $member->domicile_id;
        $this->bank_name      = $member->bank_name;
        $this->account_number = $member->account_number;
        $this->account_name   = $member->account_name;

        // Foto profil
        $this->profile_picture = $member->profile_picture;

        // Simpan object untuk card
        $this->province = $member->province;
        $this->domicile = $member->domicile;

        // Buka modal
        $this->isCardOpen = true;
    }

    protected function rules()
    {
        return [
            'name'              => 'required|string',
            'email'             => 'required|email|unique:users,email,' . $this->user_id,
            'password'          => $this->member_id ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
            'nik'               => 'required|string|unique:members,nik,' . $this->member_id,
            'phone_number'      => 'required|string',
            'gender'            => 'required|in:male,female',
            'address'           => 'required|string',
            'birth_date'        => 'required|date',
            'npwp'              => 'nullable|string|unique:members,npwp,' . $this->member_id,
            'province_id'       => 'required|exists:provinces,id',
            'domicile_id'       => 'required|exists:domiciles,id',
            'bank_name'         => 'required|string',
            'account_number'    => 'required|string',
            'account_name'      => 'required|string',
            'profile_picture'   => $this->member_id ? 'nullable|image|max:1024' : 'required|image|max:1024',
            'parent_member_id'  => 'nullable|exists:members,id'
        ];
    }



    protected function formData()
    {
        return [
            'name'           => $this->name,
            'email'          => $this->email,
            'password'       => $this->password,
            'nik'            => $this->nik,
            'phone_number'   => $this->phone_number,
            'gender'         => $this->gender,
            'address'        => $this->address,
            'birth_date'     => $this->birth_date,
            'npwp'           => $this->npwp,
            'province_id'    => $this->province_id,
            'domicile_id'    => $this->domicile_id,
            'bank_name'      => $this->bank_name,
            'account_number' => $this->account_number,
            'account_name'   => $this->account_name,
        ];
    }

    protected function afterSave($created)
    {
        $this->closeModal();
        $this->resetInput();

        $message = $created
            ? 'Member berhasil ditambahkan!'
            : 'Member berhasil diupdate!';

        $this->dispatch('success', [
            'type' => 'success',
            'message' => $message,
        ]);
    }
}
