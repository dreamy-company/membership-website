<?php

namespace App\Livewire\Admin\Withdrawals;

use App\Models\Member;
use Livewire\Component;
use App\Models\Withdrawal;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $withdrawal_id;
    public $member_id;
    public $withdrawal_amount;
    public $payment_receipt;
    public $members;
    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Withdrawal Management";

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->members = Member::with('user')->get();
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
            $this->withdrawal_amount = $withdrawal->withdrawal_amount;
            $this->payment_receipt = $withdrawal->payment_receipt;
        }
        $this->isOpen = true;
        
    }

    public function closeModal()
    {
       
        $this->isOpen = false;
    }

    private function resetInput()
    {
        $this->withdrawal_amount = '';
        $this->payment_receipt = '';
        $this->member_id = null;
        $this->withdrawal_id = null;
    }

    public function store()
    {
        $this->validate($this->rules());

        $withdrawal = Withdrawal::updateOrCreate(
            ['id' => $this->withdrawal_id],
            $this->formData()
        );

        $this->afterSave($withdrawal->wasRecentlyCreated);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        Withdrawal::findOrFail($this->confirmingDelete)->delete();

        $this->dispatch('success', [
            'type' => 'success',
            'message' => 'Withdrawal berhasil dihapus!',
        ]);
    }

    protected function rules()
    {
        return [
            'withdrawal_amount'    => 'required|numeric',
            'member_id' => 'required|integer|exists:members,id',
            'payment_receipt'   => 'required|string|max:255',
        ];
    }

    protected function formData()
    {
        return [
            'withdrawal_amount'    => $this->withdrawal_amount,
            'member_id' => $this->member_id,
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
