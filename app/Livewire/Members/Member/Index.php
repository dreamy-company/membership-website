<?php

namespace App\Livewire\Members\Member;

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
    public $search = '';
    public $id;
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
    public $isCardOpen = false;
    public $confirmingDelete;
    public $perPage = 10;

    public $users;
    public $provinces;
    public $domicilies;
    public $tree = [];
    public $title = "Member";

    public function mount()
    {
        $this->loadRoot();
        $this->users = \App\Models\User::all();
        $this->provinces = \App\Models\Province::all();
        $this->domicilies = \App\Models\Domicile::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->loadRoot();
    }

    private function loadRoot()
    {
        $roots = Member::search($this->search)
            ->where('parent_member_id', auth()->user()->id)
            ->where('user_id', '!=', auth()->user()->id)
            ->get();

        $this->tree = $roots->map(fn($m) => $this->formatNode($m, 1))->toArray();
    }

    private function formatNode($m, $level = 1)
    {
        return [
            'id' => $m->id,
            'user_id' => $m->user_id,
            'member_code' => $m->member_code,
            'phone_number' => $m->phone_number,
            'user' => [
                'name' => $m->user->name ?? 'Tanpa Nama',
                'profile_picture' => $m->profile_picture ?? null,
            ],
            'children' => [],
            'expanded' => false,
            'loading' => false,
            'fetched' => false,
            'level' => $level,
        ];
    }

    public function toggleNode($memberId)
    {
        $this->updateNode($this->tree, $memberId, function (&$node) {

            // Stop jika mencapai level 5
            if ($node['level'] >= 5) {
                // bisa kasih notifikasi, bisa juga diam saja
                return;
            }

            if (!$node['fetched']) {
                $node['loading'] = true;

                $children = Member::where('parent_member_id', $node['id'])
                    ->with('user')
                    ->get();

                $node['children'] = $children->map(
                    fn($m) =>
                    $this->formatNode($m, $node['level'] + 1)
                )->toArray();

                $node['fetched'] = true;
                $node['loading'] = false;
            }

            $node['expanded'] = !$node['expanded'];
        });
    }


    private function updateNode(&$nodes, $id, $callback)
    {
        foreach ($nodes as &$node) {

            if ($node['id'] == $id) {
                $callback($node);
                return true;
            }

            if (!empty($node['children'])) {
                if ($this->updateNode($node['children'], $id, $callback)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function openModal($id = null)
    {
        $this->resetInput();

        if ($id) {
            $member = Member::findOrFail($id);

            $this->name = $member->user->name;
            $this->email = $member->user->email;
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
            $this->old_profile_picture = $member->profile_picture;
        }

        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function openCardModal($memberId)
    {
        $member = Member::with(['province', 'domicile'])->findOrFail($memberId);

        // Isi semua variabel
        $this->id             = $member->id;
        $this->name           = $member->user->name;
        $this->email          = $member->user->email;
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


    public function closeCardModal()
    {
        $this->isCardOpen = false;
    }

    private function resetInput()
    {
        $this->member_code = '';
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
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
        $this->validate($this->rules());

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ]);

            // Handle profile picture upload
            $filename = $this->old_profile_picture;

            if ($this->profile_picture instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                // hapus file lama jika ada
                if ($this->old_profile_picture && Storage::disk('public')->exists($this->old_profile_picture)) {
                    Storage::disk('public')->delete($this->old_profile_picture);
                }

                $filename = $this->profile_picture->store('members', 'public');
            }

            $province = Province::find($this->province_id);
            $member_code = $province->code . '-' . (strlen($province->code) === 3 ? '0' : '') . str_pad(Member::count() + 1, 4, '0', STR_PAD_LEFT);

            $member = Member::create([
                'member_code' => $member_code,
                'nik' => $this->nik,
                'user_id' => $user->id,
                'parent_member_id' => auth()->user()->id,
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

            $this->afterSave(!$this->member_id);
            $this->loadRoot();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('error', [
                'type' => 'error',
                'message' => 'Gagal menambahkan member: ' . $e->getMessage(),
            ]);
        }
    }

    public function update(string $memberId)
    {
        $member = Member::findOrFail($memberId);

        $this->validate($this->updateRules($member->user_id));

        try {
            DB::beginTransaction();

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
            $member->user->update([
                'name' => $this->name,
                'email' => $this->email,
            ]);

            // Update password jika ada perubahan
            if (!empty($this->password)) {
                $member->user->update(['password' => $this->password]);
            }

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

            $this->afterSave(false);
            $this->loadRoot();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('error', [
                'type' => 'error',
                'message' => 'Gagal mengupdate member: ' . $e->getMessage(),
            ]);
        }
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

    protected function rules()
    {
        return [
            'name'           => 'required|string',
            'email'          => 'required|email|unique:users,email,',
            'password'       => 'required|string|min:8|confirmed',
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
            'profile_picture' => 'required|image|max:1024', // jpg, png, dll max 1MB
        ];
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
            'profile_picture' => 'nullable|image|max:1024', // jpg, png, dll max 1MB
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

    public function render()
    {
        $totalMembers = Member::where('parent_member_id', auth()->user()->id)->count();
        return view('livewire.members.member.index', [
            'members' => $this->tree,
            'totalMembers' => $totalMembers,
        ]);
    }
}
