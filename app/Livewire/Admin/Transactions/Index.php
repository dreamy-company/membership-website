<?php

namespace App\Livewire\Admin\Transactions;

use App\Models\Member;
use Livewire\Component;
use App\Models\Business;
use App\Models\Transaction;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $transaction_id;
    public $businesses_id;
    public $member_id;
    public $transaction_code;
    public $transaction_date;
    public $amount;
    public $hpp;
    public $balance;
    public $bonus;

    public $members;
    public $businesses;

    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Transaction Management";

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->businesses = Business::all();
        $this->members = Member::with('user')->get();
    }

    public function render()
    {
       $transactions = Transaction::search($this->search)
                      ->latest()
                      ->paginate($this->perPage);

        return view('livewire.admin.transactions.index', compact('transactions'));
    }

    public function openModal($id = null)
    {
        $this->resetInput();
        if ($id) {
            $transaction = Transaction::findOrFail($id);
            $this->transaction_id = $transaction->id;
            $this->businesses_id = $transaction->businesses_id;
            $this->member_id = $transaction->member_id;
            $this->transaction_code = $transaction->transaction_code;
            $this->transaction_date = $transaction->transaction_date;
            $this->amount = $transaction->amount;
            $this->hpp = $transaction->hpp;
            $this->balance = $transaction->balance;
            $this->bonus = $transaction->bonus;
        }
        $this->isOpen = true;
        
    }

    public function closeModal()
    {
       
        $this->isOpen = false;
    }

    private function resetInput()
    {
        $this->businesses_id = '';
        $this->member_id = '';
        $this->transaction_code = '';
        $this->transaction_date = '';
        $this->amount = '';
        $this->hpp = '';
        $this->balance = '';
        $this->bonus = '';
        $this->transaction_id = null;
    }

    public function store()
    {
        $this->validate($this->rules());

        $transaction = Transaction::updateOrCreate(
            ['id' => $this->transaction_id],
            $this->formData()
        );

        $this->afterSave($transaction->wasRecentlyCreated);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        Transaction::findOrFail($this->confirmingDelete)->delete();

        $this->dispatch('success', [
            'type' => 'success',
            'message' => 'Transaction berhasil dihapus!',
        ]);
    }

    protected function rules()
    {
        return [
            'businesses_id' => 'required|exists:businesses,id',
            'member_id' => 'required|exists:members,id',
            'transaction_code'    => 'required|string|unique:transactions,transaction_code,' . $this->transaction_id,
            'transaction_date' => 'required|date',
            'amount'   => 'required|numeric',
            'hpp'   => 'required|numeric',
            'balance'   => 'required|numeric',
            'bonus'   => 'required|numeric',    
        ];
    }

    protected function formData()
    {
        return [
            'businesses_id' => $this->businesses_id,
            'member_id' => $this->member_id,
            'transaction_code'    => $this->transaction_code,
            'transaction_date' => $this->transaction_date,
            'amount'   => $this->amount,
            'hpp'   => $this->hpp,
            'balance'   => $this->balance,
            'bonus'   => $this->bonus,
        ];
    }

    protected function afterSave($created)
    {
        $this->closeModal();
        $this->resetInput();

        $message = $created
            ? 'Transaction berhasil ditambahkan!'
            : 'Transaction berhasil diupdate!';

        $this->dispatch('success', [
            'type' => 'success',
            'message' => $message,
        ]);
    }
}
