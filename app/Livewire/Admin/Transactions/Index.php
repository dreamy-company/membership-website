<?php

namespace App\Livewire\Admin\Transactions;

use App\Imports\TransactionsImport;
use App\Models\ActivityLog;
use App\Models\BonusLevelSetup;
use App\Models\Business;
use App\Models\Member;
use App\Models\Transaction;
use App\Services\BonusService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
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
    public $record_id;
    public $BonusPercent; // Persentase khusus untuk baris yang diklik

    public $members;
    public $businesses;

    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Transaction Management";

    // import
    public $file;
    public $isOpenImport = false;

    // Filter Variables
    public $start_date;
    public $end_date;
    public $filter_umkm = '';
    public $filter_member_code = '';    

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    // Reset pagination ketika filter atau search diubah
    public function updated($property)
    {
        if (in_array($property, ['search', 'start_date', 'end_date', 'filter_umkm', 'filter_member_code'])) {
            $this->resetPage();
        }
    }

    public function mount()
    {
        $this->businesses = Business::all();
        $this->members = Member::with('user')->get();
    }

    public function render()
    {
        $query = Transaction::with('member.user')->search($this->search);

        if ($this->start_date && $this->end_date) {
            $query->whereBetween('transaction_date', [$this->start_date, $this->end_date]);
        } elseif ($this->start_date) {
            $query->whereDate('transaction_date', '>=', $this->start_date);
        } elseif ($this->end_date) {
            $query->whereDate('transaction_date', '<=', $this->end_date);
        }

        if (!empty($this->filter_umkm)) {
            $query->where('business_id', $this->filter_umkm);
        }

        if (!empty($this->filter_member_code)) {
            $query->whereHas('member', function ($q) {
                $q->where('member_code', 'like', '%' . $this->filter_member_code . '%');
            });
        }

        $transactions = $query
            ->select('member_id')
            ->selectRaw('MAX(id) as id')
            ->selectRaw('MAX(transaction_code) as transaction_code')
            ->selectRaw('MAX(transaction_date) as transaction_date')
            ->selectRaw('SUM(amount) as amount')
            ->groupBy('member_id')
            ->orderByDesc('transaction_date')
            ->paginate($this->perPage);

        return view('livewire.admin.transactions.index', compact('transactions'));
    }

    public function openModal($id = null)
    {
        $this->resetInput();
        if ($id) {
            $transaction = Transaction::findOrFail($id);
            $this->transaction_id = $transaction->id;
            $this->record_id = $transaction->id;
            $this->business_id = $transaction->business_id;
            $this->member_id = $transaction->member_id;
            $this->transaction_code = $transaction->transaction_code;
            $this->transaction_date = $transaction->transaction_date;
            $this->amount = $transaction->amount;
            $this->hpp = $transaction->hpp;
            $this->balance = $transaction->balance;
            $this->bonus = $transaction->bonus;
            $this->BonusPercent = $transaction->BonusPercent; 
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
        $this->record_id = null;
        $this->member_id = '';
        $this->transaction_code = '';
        $this->transaction_date = '';
        $this->amount = '';
        $this->hpp = '';
        $this->balance = '';
        $this->bonus = '';
        $this->transaction_id = null;
        $this->BonusPercent = 0;
    }

    public function store()
    {
        $isEditing = !empty($this->record_id);
        
        // Mencegah proses save jika dalam mode Edit (karena mode Edit = Read Only)
        if ($isEditing) {
            $this->closeModal();
            return;
        }

        $rules = [
            'business_id'      => 'required|exists:businesses,id',
            'member_id'        => 'required|exists:members,id',
            'transaction_date' => 'required|date',
            'amount'           => 'required|numeric',
            'hpp'              => 'required|numeric',
            'balance'          => 'required|numeric',
            'bonus'            => 'required|numeric',
            'transaction_code' => [
                'required',
                'string',
                Rule::unique('transactions', 'transaction_code')->where('LevelMember', 'Leader')
            ]
        ];

        $this->validate($rules);

        DB::transaction(function () {
            $baseBonusInput = $this->bonus; 
            
            $shopper = Member::find($this->member_id);
            $leaderSetup = BonusLevelSetup::where('kodeBonus', 'Leader')->first();
            
            $leaderPercent = $leaderSetup ? $leaderSetup->persenBonus : 0;
            $calculatedLeaderBonus = $baseBonusInput * ($leaderPercent / 100);

            $transaction = Transaction::create([
                'business_id'      => $this->business_id,
                'member_id'        => $this->member_id,
                'user_id'          => $shopper->user_id,
                'transaction_id'   => $shopper->user_id,
                'transaction_code' => $this->transaction_code,
                'transaction_date' => $this->transaction_date,
                'amount'           => $this->amount,
                'hpp'              => $this->hpp,
                'balance'          => $this->balance,
                'LevelMember'      => 'Leader',
                'BonusPercent'     => $leaderPercent,
                'bonus'            => $calculatedLeaderBonus,
            ]);

            if ($baseBonusInput > 0) {
                (new BonusService())->distributeBonus($transaction->id, $baseBonusInput);
            }

            $this->afterSave(true);
        });
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

    protected function afterSave($created)
    {
        $this->closeModal();
        $this->resetInput();

        $this->dispatch('success', [
            'type' => 'success',
            'message' => 'Transaction berhasil ditambahkan!',
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
            $this->dispatch('error', [
                'type' => 'error',
                'message' => 'Gagal mengimport data: ' . $e->getMessage(),
            ]);
        }

        $this->reset('file');
        $this->isOpenImport = false;
    }

    public function redirectToActivityLog()
    {
        return redirect()->route('admin.activity-log');
    }
}