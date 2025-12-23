<?php

namespace App\Livewire\Admin\Withdrawals;

use App\Models\Member;
use Livewire\Component;
use App\Models\Withdrawal;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $withdrawal_id;
    public $member_id;
    public $amount;
    public $date;
    public $payment_receipt;
    public $members;
    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Withdrawal Management";
    public $oldImage;
    public $image;

    public $old_payment_receipt;


    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount($withdrawal = null)
    {
        $this->members = Member::with('user')->get();
        if ($withdrawal) {
            $this->old_payment_receipt = $withdrawal->payment_receipt ? asset('storage/' . $withdrawal->payment_receipt) : null;
        }
    }

    public function render()
    {
       $withdrawals = Withdrawal::search($this->search)
                      ->latest()
                      ->paginate($this->perPage);

        return view('livewire.admin.withdrawals.index', compact('withdrawals'));
    }

   public function openModal($id = null)
    {
        $this->resetInput();

        if ($id) {
            $withdrawal = Withdrawal::findOrFail($id);
            $this->withdrawal_id = $withdrawal->id;
            $this->member_id = $withdrawal->member_id;
            $this->amount = $withdrawal->amount;
            $this->date = $withdrawal->date;
            $this->old_payment_receipt = $withdrawal->payment_receipt; // simpan path lama
        }

        $this->isOpen = true;
    }


    public function closeModal()
    {
       
        $this->isOpen = false;
    }

   private function resetInput()
    {
        $this->amount = '';
        $this->date = '';
        $this->payment_receipt = null;
        $this->old_payment_receipt = null;
        $this->member_id = null;
        $this->withdrawal_id = null;
    }

    public function store()
    {
        $this->validate([
            'amount' => 'required|numeric',
            'member_id' => 'required|integer|exists:members,id',
            'date' => 'required|date',
            'payment_receipt' => $this->withdrawal_id
                ? 'nullable|file|mimes:jpg,jpeg,png,pdf|max:1024'
                : 'required|file|mimes:jpg,jpeg,png,pdf|max:1024',
        ]);

        $filename = $this->old_payment_receipt;

        if ($this->payment_receipt instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $filename = $this->payment_receipt->store('withdrawals', 'public');
        }

        Withdrawal::updateOrCreate(
            ['id' => $this->withdrawal_id],
            [
                'amount' => $this->amount,
                'member_id' => $this->member_id,
                'date' => $this->date,
                'payment_receipt' => $filename,
            ]
        );

        $this->afterSave(!$this->withdrawal_id);
    }



    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        $withdrawal = Withdrawal::findOrFail($this->confirmingDelete);

        // Hapus file payment_receipt jika ada
        if ($withdrawal->payment_receipt && Storage::disk('public')->exists($withdrawal->payment_receipt)) {
            Storage::disk('public')->delete($withdrawal->payment_receipt);
        }

        // Hapus record
        $withdrawal->delete();

        $this->dispatch('success', [
            'type' => 'success',
            'message' => 'Withdrawal berhasil dihapus!',
        ]);
    }

    protected function rules()
    {
        return [
            'amount'    => 'required|numeric',
            'member_id' => 'required|integer|exists:members,id',
            'date' => 'required|date',
            'payment_receipt'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:1024',
        ];
    }

    protected function formData()
    {
        return [
            'amount'    => $this->amount,
            'member_id' => $this->member_id,
            'date' => $this->date,
            'payment_receipt'   => $this->payment_receipt,
        ];
    }

    protected function afterSave($created)
    {
        $this->closeModal();
        $this->resetInput();

        $message = $created
            ? 'Withdrawal berhasil ditambahkan!'
            : 'Withdrawal berhasil diupdate!';

        $this->dispatch('success', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

}
