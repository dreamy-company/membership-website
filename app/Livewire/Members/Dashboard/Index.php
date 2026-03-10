<?php

namespace App\Livewire\Members\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

// Models
use App\Models\BonusLog;
use App\Models\Transaction;
use App\Models\Withdrawal;

class Index extends Component
{
    use WithPagination;

    // Properties
    public $search = '';
    public $perPage = 10;
    
    // UI Properties
    public $isOpen = false;
    public $confirmingDelete;
    public $title = "Member Dashboard";

    // Settings
    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        
        // Pastikan user punya data member
        if (!$user->member) {
            abort(403, 'Akun anda belum terdaftar sebagai Member.');
        }

        $myMember = $user->member;
        $myMemberId = $myMember->id;

        // ==========================================
        // 1. DATA TRANSAKSI (BONUS MASUK)
        // ==========================================
        $transactionsQuery = Transaction::with(['sourceMember.user', 'business'])
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
        // 2. DATA WITHDRAWAL (PENARIKAN)
        // ==========================================
        $withdrawalsQuery = Withdrawal::where('member_id', $myMemberId);

        if ($this->search) {
            $withdrawalsQuery->where('amount', 'like', '%' . $this->search . '%');
        }

        $withdrawals = $withdrawalsQuery->get()->map(function ($item) {
            $item->log_type = 'withdrawal';
            $item->created_at = $item->date; // Samakan agar bisa diurutkan dengan mudah
            return $item;
        });


        // ==========================================
        // 3. GABUNGKAN & PAGINASI MANUAL
        // ==========================================
        $allLogs = $transactions->concat($withdrawals)->sortByDesc('created_at')->values();

        $page = Paginator::resolveCurrentPage() ?: 1;
        $paginatedLogs = new LengthAwarePaginator(
            $allLogs->forPage($page, $this->perPage), 
            $allLogs->count(), 
            $this->perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()] 
        );


        // ==========================================
        // 4. STATISTIK KEUANGAN
        // ==========================================
        $mySpendingTotal = Transaction::where('member_id', $myMemberId)
                            ->where('LevelMember', 'Leader') 
                            ->sum('amount');
        
        $totalBonusIncome = Transaction::where('member_id', $myMemberId)->sum('bonus'); 
        
        // [PERBAIKAN] Ambil total penarikan yang sesungguhnya dari database
        $totalWithdrawn = Withdrawal::where('member_id', $myMemberId)->sum('amount'); 
        
        // Saldo bersih
        $currentBalance = $totalBonusIncome - $totalWithdrawn;


        // ==========================================
        // 5. STATISTIK JARINGAN (NETWORK)
        // ==========================================
        $networkStats = $myMember->getNetworkStats(5); 
        $totalMembers = array_sum($networkStats);      

        return view('dashboard', [ // Sesuaikan nama view jika berbeda
            'transactions'      => $paginatedLogs, // Kirim data yang sudah digabung
            
            'transactionTotal'  => $mySpendingTotal,
            'bonusTotal'        => $totalBonusIncome,
            'withdrawnTotal'    => $totalWithdrawn,
            'currentBalance'    => $currentBalance, 
            
            'totalMembers'      => $totalMembers,
            'networkStats'      => $networkStats
        ]);
    }
}