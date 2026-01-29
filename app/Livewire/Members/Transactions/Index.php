<?php

namespace App\Livewire\Members\Transactions;


use Livewire\Component;
use App\Models\BonusLog;

use App\Models\Transaction;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $name;
    public $perPage = 10;
    public $title = "Transactions";

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function mount()
    {

    }

   public function render()
    {
        $myMemberId = auth()->user()->member->id;

        // QUERY BONUS LOG
        $transactions = BonusLog::with(['sourceMember.user', 'transaction.business'])
            ->where('member_id', $myMemberId) // Hanya bonus yang masuk ke dompet SAYA
            ->where(function ($query) {
                // Fitur Search: Bisa cari nama Downline atau Kode Transaksi
                if ($this->search) {
                    $query->whereHas('sourceMember.user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('transaction', function ($q) {
                        $q->where('transaction_code', 'like', '%' . $this->search . '%');
                    });
                }
            })
            ->latest()
            ->paginate($this->perPage);

        // Hitung Total Bonus yang sudah didapat (Semua halaman)
        $transactionTotal = BonusLog::where('member_id', $myMemberId)->sum('amount');

        return view('livewire.members.transactions.index', compact('transactions', 'transactionTotal'));
    }
}
