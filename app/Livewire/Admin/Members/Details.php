<?php

namespace App\Livewire\Admin\Members;

use App\Models\User;
use App\Models\Member;
use Livewire\Component;
use App\Models\Domicile;
use App\Models\Province;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Details extends Component
{
    use WithPagination, WithFileUploads;
    public $search = '';
    public $id;
    // [BARU] Simpan ID node yang sedang terbuka
    public $expandedNodes = [];
    public $province;
    public $domicile;

    public $member_id;
    public $member_code;
    public $name;
    public $parentName;
    public $email;
    public $password;
    public $password_confirmation;
    public $viewType = 'list'; // Default 'list' (tampilan lama) atau 'chart' (tampilan tree baru)
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
    public $profile_picture; // untuk upload file
    public $old_profile_picture; // untuk preview saat edit

    public $isOpen = false;
    public $isCardOpen = false;
    public $confirmingDelete;
    public $perPage = 10;

    public $memberUserId;
    public $parentUserId;

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

    public function loadRoot($memberId = null)
    {
        if ($memberId) {

            // parent
            $parent = Member::where('user_id', $memberId)->first();

            if (!$parent) {
                $this->tree = [];
                return;
            }

            // child (JANGAN pakai search di sini)
            $childrenQuery = Member::where('parent_user_id', $memberId)
                ->where('user_id', '!=', $memberId);

            if ($this->search) {
                $childrenQuery->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('user_id', 'like', "%{$this->search}%");
                });
            }

            $children = $childrenQuery->get();

            $node = $this->formatNode($parent, 1);
            $node['children'] = $children
                ->map(fn ($c) => $this->formatNode($c, 2))
                ->toArray();

            $this->tree = [$node];

        } else {

            // root level
            $rootsQuery = Member::whereColumn('parent_user_id', 'user_id');

            if ($this->search) {
                $rootsQuery->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('user_id', 'like', "%{$this->search}%");
                });
            }

            $this->tree = $rootsQuery
                ->get()
                ->map(fn ($m) => $this->formatNode($m, 1))
                ->toArray();
        }
    }



    private function formatNode($m, $level = 1)
    {
        $isExpanded = in_array($m->user_id, $this->expandedNodes);

        $children = [];
        $fetched = false;

        if ($isExpanded) {
            $childModels = Member::where('parent_user_id', $m->user_id)
                ->where('user_id', '!=', $m->user_id)
                ->with('user')
                ->get();

            $children = $childModels->map(
                fn ($child) => $this->formatNode($child, $level + 1)
            )->toArray();

            $fetched = true;
        }

        return [
            'id' => $m->user_id, // ⬅️ KONSISTEN
            'user_id' => $m->user_id,
            'member_code' => $m->member_code,
            'phone_number' => $m->phone_number,
            'parent_user_id' => $m->parent_user_id,
            'user' => [
                'name' => $m->user->name ?? 'Tanpa Nama',
                'profile_picture' => $m->profile_picture ?? null,
            ],
            'children' => $children,
            'expanded' => $isExpanded,
            'loading' => false,
            'fetched' => $fetched,
            'level' => $level,
        ];
    }


    public function toggleNode($memberId)
    {
        // [BARU] Logika state management
        if (in_array($memberId, $this->expandedNodes)) {
            // Jika sudah ada, hapus (Collapse)
            $this->expandedNodes = array_diff($this->expandedNodes, [$memberId]);
        } else {
            // Jika belum ada, tambah (Expand)
            $this->expandedNodes[] = $memberId;
        }

        // Refresh tree untuk menerapkan perubahan tampilan
        $this->loadRoot($memberId);
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
            $this->parent_user_id = $member->parent_user_id;
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

    public function openMemberModal($id = null)
    {
        $this->resetInput();

        $user = User::findOrFail($id);

        $this->parent_user_id = $id;
        $this->parentName = $user->name;

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
        $this->parent_user_id = '';
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

            $parentUserId = null;
            if ($this->parent_user_id) {
                $parentUserId = $this->parent_user_id;
            } else {
                $parentUserId = auth()->user()->id;
            }

            $member = Member::create([
                'member_code' => $member_code,
                'nik' => $this->nik,
                'user_id' => $user->id,
                'parent_user_id' => $parentUserId,
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

            // [BARU] Setelah sukses tambah, kita harus memastikan Parent-nya Expanded
            // Agar user langsung melihat data yang baru ditambahkan
            $parentMember = Member::where('user_id', $parentUserId)->first();
            if ($parentMember && !in_array($parentMember->id, $this->expandedNodes)) {
                $this->expandedNodes[] = $parentMember->id;
            }

            $this->afterSave(!$this->member_id);

            // Reload tree akan membaca $expandedNodes yang baru
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

            // Di akhir update:
            $this->afterSave(false);

            // Saat loadRoot dipanggil, dia akan merender ulang tree 
            // dengan posisi expand/collapse yang sama persis seperti sebelum tombol edit ditekan
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

        // Load root akan mempertahankan state parent yang terbuka
        $this->loadRoot();
    }

    protected function rules()
    {
        return [
            'name'           => 'required|string',
            'email'          => 'required|email|unique:users,email,',
            'password'       => 'required|string|min:8|confirmed',
            'nik'            => 'required|string|numeric|unique:members,nik,' . $this->member_id,
            'phone_number'   => 'required|string|numeric',
            'gender'         => 'required|in:male,female',
            'address'        => 'required|string',
            'birth_date'     => 'required|date',
            'npwp'           => 'nullable|string|numeric|unique:members,npwp,' . $this->member_id,
            'province_id'    => 'required|exists:provinces,id',
            'domicile_id'    => 'required|exists:domiciles,id',
            'bank_name'      => 'required|string',
            'account_number' => 'required|string',
            'account_name'   => 'required|string',
            'profile_picture' => 'nullable|image|max:2048', // jpg, png, dll max 1MB
        ];
    }

    protected function updateRules($userId)
    {
        return [
            'name'           => 'required|string',
            'email'          => 'required|email|unique:users,email,' . $userId . ',id',
            'password'       => 'nullable|string|min:8|confirmed',
            'nik'            => 'required|string|numeric|unique:members,nik,' . $this->member_id,
            'phone_number'   => 'required|string|numeric',
            'gender'         => 'required|in:male,female',
            'address'        => 'required|string',
            'birth_date'     => 'required|date',
            'npwp'           => 'nullable|string|numeric|unique:members,npwp,' . $this->member_id,
            'province_id'    => 'required|exists:provinces,id',
            'domicile_id'    => 'required|exists:domiciles,id',
            'bank_name'      => 'required|string',
            'account_number' => 'required|string',
            'account_name'   => 'required|string',
            'profile_picture' => 'nullable|image|max:2048', // jpg, png, dll max 1MB
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

    // Tambahkan method untuk ganti view
    public function switchView($type)
    {
        $this->viewType = $type;
    }

    public function render()
    {
        $totalMembers = Member::where('parent_user_id', 102)->count();
        return view('livewire.admin.members.details', [
            'members' => $this->tree,
            'totalMembers' => $totalMembers,
        ]);
    }

}
