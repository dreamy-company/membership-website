<?php

namespace App\Livewire\Admin\Members;

use App\Models\Member;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
     use WithPagination;

    public $search = '';
    public $member_id;
    public $member_code;
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
    public $profile_picture;
    
    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Member";

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

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
            $this->profile_picture = $member->profile_picture;
        }
        $this->isOpen = true;
        
    }

    public function closeModal()
    {
       
        $this->isOpen = false;
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
        $this->profile_picture = '';
        $this->member_id = null;
    }

    public function store()
    {
        $this->validate($this->rules());

        $member = Member::updateOrCreate(
            ['id' => $this->member_id],
            $this->formData()
        );

        $this->afterSave($member->wasRecentlyCreated);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        Member::findOrFail($this->confirmingDelete)->delete();

        $this->dispatch('success', [
            'type' => 'success',
            'message' => 'Member berhasil dihapus!',
        ]);
    }

    protected function rules()
    {
        return [
            'member_code'    => 'required|string|unique:members,member_code,' . $this->member_id,
            'nik'            => 'required|string|unique:members,nik,' . $this->member_id,
            'user_id'        => 'required|exists:users,id',
            'parent_member_id' => 'nullable|exists:members,id',
            'phone_number'   => 'required|string',
            'gender'         => 'required|in:male,female',
            'address'        => 'required|string',
            'birth_date'     => 'required|date',
            'npwp'           => 'nullable|string|unique:members,npwp,' . $this->member_id,
            'province_id'    => 'required|exists:provinces,id',
            'domicile_id'    => 'required|exists:domicilies,id',
            'bank_name'      => 'nullable|string',
            'account_number' => 'nullable|string',
            'account_name'   => 'nullable|string',
            'profile_picture'=> 'nullable|string',
        ];
    }

    protected function formData()
    {
        return [
            'member_code'    => $this->member_code,
            'nik'            => $this->nik,
            'user_id'        => $this->user_id,
            'parent_member_id' => $this->parent_member_id,
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
            'profile_picture'=> $this->profile_picture,
        ];
    }

    protected function afterSave($created)
    {
        $this->closeModal();
        $this->resetInput();

        $message = $created
            ? 'Business berhasil ditambahkan!'
            : 'Business berhasil diupdate!';

        $this->dispatch('success', [
            'type' => 'success',
            'message' => $message,
        ]);
    }
}
