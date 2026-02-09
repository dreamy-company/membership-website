<?php

namespace App\Livewire\Members\Transactions;


use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;

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

        // 1. QUERY DATA TRANSAKSI (BONUS LOG)
        // Kita load 'sourceMember.user' untuk mengambil nama Downline yang belanja
        $transactions = Transaction::with(['sourceMember.user', 'business'])
            ->where('member_id', $myMemberId) // Ambil yang penerimanya adalah SAYA
            ->where(function (Builder $query) {
                if ($this->search) {
                    $query->whereHas('sourceMember.user', function ($q) {
                        // Search Nama Downline
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('business', function ($q) {
                        // Search Nama Toko
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('transaction_code', 'like', '%' . $this->search . '%')
                    ->orWhere('LevelMember', 'like', '%' . $this->search . '%'); 
                }
            })
            ->latest()
            ->paginate($this->perPage);

        // 2. HITUNG TOTAL INCOME (UANG MASUK)
        // Gunakan 'bonus', JANGAN 'amount'
        $totalIncome = Transaction::where('member_id', $myMemberId)->sum('bonus');

        // 3. (Opsional) HITUNG TOTAL OMZET DOWNLINE
        // Gunakan 'amount' tapi hanya untuk downline (Level 2 ke atas) jika mau omzet murni
        $totalOmzet = Transaction::where('member_id', $myMemberId)->sum('amount');

        return view('livewire.members.transactions.index', [
            'transactions' => $transactions,
            'totalIncome'  => $totalIncome,
            'totalOmzet'   => $totalOmzet
        ]);
    }
}
