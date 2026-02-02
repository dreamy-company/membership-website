<?php

namespace App\Livewire\Admin\Members;

use App\Models\User;
use App\Models\Member;
use Livewire\Component;
use App\Models\Province;
use App\Models\Withdrawal;
use App\Models\ActivityLog;
use App\Models\Domicile;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
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
    public $status; // untuk status member
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

    public $openWithdrawalModal = false;
    public $member_name;
    public $bonus;
    public $withdrawal_amount;
    public $payment_receipt;
    public $old_payment_receipt;

    public $searchCode = '';
    public $searchName = '';
    public $searchGender = '';
    public $searchAddress = '';
    public $searchBank = '';

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

    public function updated($property)
    {
        // Cek jika properti yang berubah adalah salah satu filter
        if (in_array($property, ['searchCode', 'searchName', 'searchGender', 'searchAddress', 'searchBank'])) {
            $this->resetPage();
        }
    }

    // Fitur Reset semua filter
    public function resetFilters()
    {
        $this->reset(['searchCode', 'searchName', 'searchGender', 'searchAddress', 'searchBank']);
        $this->resetPage();
    }

    public function render()
    {
        $members = Member::with('user')
            // 2.2.1 Filter Member Code
            ->when($this->searchCode, function ($q) {
                $q->where('member_code', 'like', '%' . $this->searchCode . '%');
            })
            // 2.2.2 Filter Nama (Relasi ke User)
            ->when($this->searchName, function ($q) {
                $q->whereHas('user', function ($u) {
                    $u->where('name', 'like', '%' . $this->searchName . '%');
                });
            })
            // 2.2.3 Filter Gender (Exact Match)
            ->when($this->searchGender, function ($q) {
                $q->where('gender', $this->searchGender);
            })
            // 2.2.4 Filter Address
            ->when($this->searchAddress, function ($q) {
                $q->where('address', 'like', '%' . $this->searchAddress . '%');
            })
            // 2.2.5 Filter Bank (Mencakup Nama Bank, No Rek, atau Atas Nama)
            ->when($this->searchBank, function ($q) {
                $q->where(function($sub) {
                    $sub->where('bank_name', 'like', '%' . $this->searchBank . '%')
                        ->orWhere('account_number', 'like', '%' . $this->searchBank . '%')
                        ->orWhere('account_name', 'like', '%' . $this->searchBank . '%');
                });
            })
            ->latest()
            ->paginate(10);

          return view('livewire.admin.members.index', [
            'members' => $members
        ]);
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

    public function closeModal()
    {
        $this->isOpen = false;
        $this->openWithdrawalModal = false;
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
        $this->parent_user_id = '';
        $this->phone_number = '';
        $this->gender = '';
        $this->address = '';
        $this->birth_date = '';
        $this->npwp = '';
        $this->status = '';
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

        // Gunakan Transaction agar Data User & Member Konsisten
        DB::transaction(function () {
            
            // ==========================
            // 1. HANDLE USER (Login Info)
            // ==========================
            
            // Siapkan data user dasar
            $userData = [
                'name'  => $this->name,
                'email' => $this->email,
            ];

            // Logic Password: Hanya update jika input tidak kosong
            // Jika create baru & kosong, password akan kosong (atau handle validasi required di rules)
            if (!empty($this->password)) {
                $userData['password'] = bcrypt($this->password);
            }

            $user = User::updateOrCreate(
                ['id' => $this->user_id], 
                $userData
            );

            // ==========================
            // 2. HANDLE PROFILE PICTURE
            // ==========================
            $filename = $this->old_profile_picture;

            if ($this->profile_picture instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                
                // Hapus file lama jika ada dan bukan default
                if ($this->old_profile_picture && Storage::disk('public')->exists($this->old_profile_picture)) {
                    Storage::disk('public')->delete($this->old_profile_picture);
                }

                $filename = $this->profile_picture->store('members', 'public');
            }

            // ==========================
            // 3. GENERATE MEMBER CODE
            // ==========================
            // Code hanya digenerate jika Member Baru
            if ($this->member_id) {
                // Jika Update, ambil code lama (jangan berubah)
                $member_code = Member::find($this->member_id)->member_code;
            } else {
                // Jika Create, generate baru
                $domicile = Domicile::find($this->domicile_id);
                $prefix = $domicile ? $domicile->code : 'MBR'; // Fallback jika domisili kosong
                
                // Handle jika tabel kosong (max id null) -> jadi 0 + 1
                $nextId = (Member::max('id') ?? 0) + 1;
                
                $member_code = $prefix . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }

            // ==========================
            // 4. HANDLE MEMBER (Detail Info)
            // ==========================
            Member::updateOrCreate(
                ['id' => $this->member_id],
                [
                    'user_id'          => $user->id,
                    'member_code'      => $member_code,
                    
                    // [FIX] Masukkan Status yang baru kita buat inputnya
                    'status'           => $this->status ?? 'active', 

                    // [FIX] Parent ID diambil dari inputan form ($this->parent...), BUKAN $user->id
                    'parent_user_id'   => $this->parent_user_id ?: null, 

                    'nik'              => $this->nik,
                    'gender'           => $this->gender,
                    'phone_number'     => $this->phone_number,
                    'address'          => $this->address,
                    'birth_date'       => $this->birth_date,
                    'province_id'      => $this->province_id,
                    'domicile_id'      => $this->domicile_id,
                    'bank_name'        => $this->bank_name,
                    'account_number'   => $this->account_number,
                    'account_name'     => $this->account_name,
                    'npwp'             => $this->npwp,
                    'status'           => $this->status,
                    'profile_picture'  => $filename,
                ]
            );
        });

        // Reset Input & Tutup Modal
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
            'parent_user_id'  => 'nullable|exists:users,id'
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

    public function openWithdrawal($memberId)
    {
        $this->member_id = $memberId;

        // ambil member beserta bonusnya
        $member = Member::with('bonus')->find($memberId);

        // cek apakah member punya bonus
        if ($member->bonus === null || empty($member->bonus->balance)) {
            $this->dispatch('error', [
                'type' => 'error',
                'message' => 'Member tidak memiliki bonus untuk ditarik.',
            ]);
            return;
        }

        $this->member_name = $member->user->name;           // nama member
        $this->bonus = $member->bonus->balance ?? 0; // bonus saat ini
        $this->withdrawal_amount = 0;                 // default bisa diisi user nanti

        $this->openWithdrawalModal = true;
    }

    public function processWithdrawal()
    {
        $this->validate([
            'withdrawal_amount' => 'required|numeric|min:1',
            'payment_receipt'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:1024',
        ]);

        $filename = null;

        DB::beginTransaction();

        try {
            $member = Member::with('bonus')
                ->lockForUpdate()
                ->findOrFail($this->member_id);

            if (!$member->bonus || $member->bonus->balance <= 0) {
                throw new \Exception('Member tidak punya bonus.');
            }

            if ($this->withdrawal_amount > $member->bonus->balance) {
                throw new \Exception('Saldo bonus tidak mencukupi.');
            }

            $balanceBefore = $member->bonus->balance;
            $balanceAfter  = $balanceBefore - $this->withdrawal_amount;

            // upload receipt
            if ($this->payment_receipt instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $filename = $this->payment_receipt->store('withdrawals', 'public');
            }

            // INSERT withdrawal history
            Withdrawal::create([
                'member_id'        => $member->id,
                'amount'           => $this->withdrawal_amount,
                'date'             => now(),
                'payment_receipt'  => $filename,
            ]);

            // UPDATE bonus balance
            $member->bonus->update([
                'balance' => $balanceAfter,
            ]);

            // activity log
            ActivityLog::create([
                'user_id'     => auth()->id(),
                'type'        => 'withdrawal',
                'description' => 'Member ' . $member->user->name .
                                ' withdrawal ' . number_format($this->withdrawal_amount),
            ]);

            DB::commit();

            $this->closeModal();
            $this->resetInput();

            $this->dispatch('success', [
                'type' => 'success',
                'message' => 'Penarikan bonus berhasil diproses!',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            if ($filename && Storage::disk('public')->exists($filename)) {
                Storage::disk('public')->delete($filename);
            }

            $this->dispatch('error', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function redirectToMemberDetails()
    {
        return redirect()->route('admin.members.detail');
    }

}
