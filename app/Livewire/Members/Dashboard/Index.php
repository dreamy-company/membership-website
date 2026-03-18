<?php

namespace App\Livewire\Members\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB; // Tambahkan DB facade

// Models
use App\Models\BonusLog;
use App\Models\Transaction;
use App\Models\Withdrawal;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    
    public $isOpen = false;
    public $confirmingDelete;
    public $title = "Member Dashboard";

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        
        if (!$user->member) {
            abort(403, 'Akun anda belum terdaftar sebagai Member.');
        }

        $myMemberId = $user->member->id;

        // ==========================================
        // 1. OPTIMASI STATISTIK KEUANGAN (1 Query untuk 2 proses)
        // ==========================================
        // Daripada query sum() 2x ke tabel transaction, kita gabung jadi 1 query
        $txStats = Transaction::where('member_id', $myMemberId)
            ->selectRaw("SUM(CASE WHEN LevelMember = 'Leader' THEN amount ELSE 0 END) as spending_total")
            ->selectRaw("SUM(bonus) as bonus_total")
            ->first();

        $mySpendingTotal  = $txStats->spending_total ?? 0;
        $totalBonusIncome = $txStats->bonus_total ?? 0;
        
        $totalWithdrawn   = Withdrawal::where('member_id', $myMemberId)->sum('amount'); 
        $currentBalance   = $totalBonusIncome - $totalWithdrawn;

        // ==========================================
        // 2. DATA TRANSAKSI (BONUS MASUK) - Hemat Memori
        // ==========================================
        $transactionsQuery = Transaction::with(['sourceMember.user:id,name', 'business:id,name'])
            // Select HANYA kolom yang dibutuhkan di view agar RAM tidak bengkak
            ->select('id', 'member_id', 'transaction_id', 'business_id', 'transaction_code', 'LevelMember', 'BonusPercent', 'bonus', 'created_at')
            ->where('member_id', $myMemberId);

        if ($this->search) {
            $transactionsQuery->where(function (Builder $query) {
                $query->whereHas('sourceMember.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('transaction_code', 'like', '%' . $this->search . '%')
                ->orWhere('LevelMember', 'like', '%' . $this->search . '%');
            });
        }

        $transactions = $transactionsQuery->get()->map(function ($item) {
            $item->log_type = 'bonus';
            return $item;
        });

        // ==========================================
        // 3. DATA WITHDRAWAL (PENARIKAN) - Hemat Memori
        // ==========================================
        $withdrawalsQuery = Withdrawal::select('id', 'member_id', 'amount', 'date')
            ->where('member_id', $myMemberId);

        if ($this->search) {
            $withdrawalsQuery->where('amount', 'like', '%' . $this->search . '%');
        }

        $withdrawals = $withdrawalsQuery->get()->map(function ($item) {
            $item->log_type = 'withdrawal';
            $item->created_at = $item->date;
            return $item;
        });

        // ==========================================
        // 4. GABUNGKAN & PAGINASI MANUAL
        // ==========================================
        $allLogs = $transactions->concat($withdrawals)->sortBy('created_at')->values();

        $page = Paginator::resolveCurrentPage() ?: 1;
        $paginatedLogs = new LengthAwarePaginator(
            $allLogs->forPage($page, $this->perPage), 
            $allLogs->count(), 
            $this->perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()] 
        );

        // ==========================================
        // 5. STATISTIK JARINGAN (NETWORK)
        // ==========================================
        $networkStats = $user->member->getNetworkStats(5); 
        $totalMembers = array_sum($networkStats);      

        return view('dashboard', [
            'transactions'     => $paginatedLogs,
            'transactionTotal' => $mySpendingTotal,
            'bonusTotal'       => $totalBonusIncome,
            'withdrawnTotal'   => $totalWithdrawn,
            'currentBalance'   => $currentBalance, 
            'totalMembers'     => $totalMembers,
            'networkStats'     => $networkStats
        ]);
    }
}