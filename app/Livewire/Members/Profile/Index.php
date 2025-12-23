<?php

namespace App\Livewire\Members\Profile;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

// Models
use App\Models\Member;
use App\Models\User;
use App\Models\Province;

class Index extends Component
{
    use WithPagination, WithFileUploads;
    // [BARU] Tambahkan variable ini untuk memaksa render ulang input
    public $formKey = 1;

    public $search = '';
    public $id;
    public $province;
    public $domicile;

    public $member_id;
    public $member_code;
    public $name;
    public $email;

    // Inisialisasi password dengan string kosong agar reactive
    public $password = '';
    public $password_confirmation = '';

    public $nik;
    public $user_id;
    public $parent_user_id;
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
    public $profile_picture;
    public $old_profile_picture;

    public $isOpen = false;
    public $isCardOpen = false;
    public $confirmingDelete;
    public $perPage = 10;

    public $users;
    public $provinces;
    public $domicilies;
    public $tree = [];
    public $title = "Profile Settings";

    public function mount()
    {
        $this->users = \App\Models\User::all();
        $this->provinces = \App\Models\Province::all();
        $this->domicilies = \App\Models\Domicile::all();

        $member = Member::findOrFail(auth()->user()->member->id);

        $this->loadMemberData($member);
    }

    public function update(string $memberId)
    {
        try {
            DB::beginTransaction();

            $member = Member::findOrFail($memberId);

            $this->validate($this->updateRules($member->user_id));

            $this->id = $member->user->id;

            // Handle profile picture upload
            $filename = $this->old_profile_picture;

            if ($this->profile_picture instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                // hapus file lama jika ada
                if ($this->old_profile_picture && Storage::disk('public')->exists($this->old_profile_picture)) {
                    Storage::disk('public')->delete($this->old_profile_picture);
                }

                $filename = $this->profile_picture->store('members', 'public');
            }

            // Update user
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            // Update password jika ada perubahan (tidak kosong)
            if (!empty($this->password)) {
                $userData['password'] = $this->password;
            }

            $member->user->update($userData);

            // Update member
            $member->update([
                'nik' => $this->nik,
                'phone_number' => $this->phone_number,
                'gender' => $this->gender,
                'address' => $this->address,
                'birth_date' => $this->birth_date,
                'province_id' => $this->province_id,
                'domicile_id' => $this->domicile_id,
                'bank_name' => $this->bank_name,
                'account_number' => $this->account_number,
                'account_name' => $this->account_name,
                'npwp' => $this->npwp,
                'profile_picture' => $filename,
            ]);

            DB::commit();

            $member->refresh();
            $this->loadMemberData($member);
            $this->afterSave(false);

            // [PERBAIKAN DISINI]
            // 1. Kosongkan variable
            $this->password = '';
            $this->password_confirmation = '';
            $this->profile_picture = null;

            // 2. [KUNCI UTAMA] Ubah key agar elemen HTML dihancurkan dan dibuat ulang
            $this->formKey++;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', [
                'type' => 'error',
                'message' => 'Gagal update data diri: ' . $e->getMessage(),
            ]);
        }
    }

    private function loadMemberData($member)
    {
        $this->member_id = $member->id;
        $this->user_id = $member->user_id;
        $this->parent_user_id = $member->parent_user_id;
        $this->member_code = $member->member_code;

        $this->name = $member->user->name;
        $this->email = $member->user->email;
        $this->nik = $member->nik;
        $this->phone_number = $member->phone_number;
        $this->gender = $member->gender;
        $this->address = $member->address;
        $this->birth_date = $member->birth_date;
        $this->province_id = $member->province_id;
        $this->domicile_id = $member->domicile_id;
        $this->bank_name = $member->bank_name;
        $this->account_number = $member->account_number;
        $this->account_name = $member->account_name;
        $this->npwp = $member->npwp;
        $this->old_profile_picture = $member->profile_picture;

        // Pastikan saat load data awal/refresh, password dikosongkan
        $this->password = '';
        $this->password_confirmation = '';
    }


    protected function updateRules($userId)
    {
        return [
            'name'           => 'required|string',
            'email'          => 'required|email|unique:users,email,' . $userId . ',id',
            'password'       => 'nullable|string|min:8|confirmed',
            'nik'            => 'required|string|unique:members,nik,' . $this->member_id,
            'phone_number'   => 'required|string',
            'gender'         => 'required|in:male,female',
            'address'        => 'required|string',
            'birth_date'     => 'required|date',
            'npwp'           => 'nullable|string|unique:members,npwp,' . $this->member_id,
            'province_id'    => 'required|exists:provinces,id',
            'domicile_id'    => 'required|exists:domiciles,id',
            'bank_name'      => 'required|string',
            'account_number' => 'required|string',
            'account_name'   => 'required|string',
            'profile_picture' => 'nullable|image|max:1024',
        ];
    }

    protected function afterSave($created)
    {
        $message = $created
            ? 'Member berhasil ditambahkan!'
            : 'Berhasil update data diri';

        $this->dispatch('success', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function render()
    {
        return view('livewire.members.profile.index');
    }
}
