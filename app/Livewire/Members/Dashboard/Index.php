<?php

namespace App\Livewire\Members\Dashboard;


use App\Models\Bonus;
use App\Models\Member;
use Livewire\Component;
use App\Models\BonusLog;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Member Dashboard";

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Ambil ID Member dari user yang login
        $myMemberId = auth()->user()->member->id; 

        // 1. QUERY UTAMA (BONUS HISTORY)
        // Kita pakai BonusLog, bukan Transaction.
        // Kita 'eager load' relasi sourceMember (downline) dan transaction (untuk info toko/nota)
        $bonusLogs = BonusLog::with(['sourceMember.user', 'transaction.business'])
            ->where('member_id', $myMemberId) // Hanya bonus milik saya
            ->where(function (Builder $query) {
                // Logika Search: Cari Nama Downline ATAU Kode Transaksi
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

        // 2. STATISTIK
        // Total belanja pribadi saya (Pengeluaran)
        $mySpendingTotal = Transaction::where('member_id', $myMemberId)->sum('amount');
        
        // Total Saldo Saat Ini (Menggunakan Virtual Attribute 'balance' di Model Member)
        $currentBalance = $bonusLogs->sum('amount'); 
        $withdrawnAmount = Withdrawal::where('member_id', $myMemberId)->sum('amount');

        // Total Downline Langsung
        $totalMembers = Member::where('parent_user_id', auth()->user()->id)->count();

        return view('dashboard', [
            'transactions' => $bonusLogs, // Variable dikirim sebagai $transactions agar view tidak perlu ubah banyak
            'transactionTotal' => $mySpendingTotal,
            'bonusTotal' => $currentBalance,
            'withdrawnTotal' => $withdrawnAmount,
            'totalMembers' => $totalMembers
        ]);
    }
}
