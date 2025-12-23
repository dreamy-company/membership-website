<?php

namespace App\Livewire\Admin\Transactions;

use App\Models\Member;
use Livewire\Component;
use App\Models\Business;
use App\Models\ActivityLog;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Imports\TransactionsImport;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $transaction_id;
    public $business_id;
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

    // import
    public $file;
    public $isOpenImport = false;


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
            $this->business_id = $transaction->business_id;
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
        $this->isOpenImport = false;
    }

    private function resetInput()
    {
        $this->business_id = '';
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
            'business_id' => 'required|exists:businesses,id',
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
            'business_id' => $this->business_id,
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

    public function openImportModal()
    {
        $this->file = null;
        $this->isOpenImport = true;
    }

    public function storeData()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        try {

            Excel::import(new TransactionsImport, $this->file->getRealPath());

            ActivityLog::create([
                'user_id' => auth()->id(),
                'type' => 'upload',
                'description' => 'Upload file Transactions via Excel',
            ]);

            $this->dispatch('success', [
                'type' => 'success',
                'message' => 'Transactions imported successfully!',
            ]);

        } catch (\Exception $e) {

            // pesan error buat user
            $this->dispatch('error', [
                'type' => 'error',
                'message' => 'Gagal mengimport data: ' . $e->getMessage(),
            ]);
        }

        // ini tetap jalan entah sukses / gagal
        $this->reset('file');
        $this->isOpenImport = false;
    }


    public function redirectToActivityLog()
    {
        return redirect()->route('admin.activity-log');
    }
}
