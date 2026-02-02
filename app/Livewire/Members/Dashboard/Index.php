<?php

namespace App\Livewire\Members\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

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
        // 1. DATA TABEL (BONUS HISTORY)
        // ==========================================
        $bonusLogs = BonusLog::with(['sourceMember.user', 'transaction.business'])
            ->where('member_id', $myMemberId)
            ->where(function (Builder $query) {
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

        // ==========================================
        // 2. STATISTIK KEUANGAN
        // ==========================================
        
        // A. Total Belanja Pribadi
        $mySpendingTotal = Transaction::where('member_id', $myMemberId)->sum('amount');
        
        // B. Total Semua Bonus Masuk (Lifetime Income)
        // PENTING: Jangan sum dari $bonusLogs karena itu data paginasi (cuma 10 data)
        $totalBonusIncome = BonusLog::where('member_id', $myMemberId)->sum('amount'); 

        // C. Total Uang Ditarik (Approved Withdrawals)
        $totalWithdrawn = Withdrawal::where('member_id', $myMemberId)->sum('amount');

        // ==========================================
        // 3. STATISTIK JARINGAN (NETWORK)
        // ==========================================
        
        // Memanggil fungsi getNetworkStats() yang ada di Model Member
        // Pastikan Model Member.php sudah diupdate sesuai diskusi sebelumnya
        $networkStats = $myMember->getNetworkStats(5); // Ambil data 5 level
        $totalMembers = array_sum($networkStats);      // Total Downline

        return view('dashboard', [
            // Data Tabel
            'transactions'      => $bonusLogs,
            
            // Data Keuangan
            'transactionTotal'  => $mySpendingTotal,
            'bonusTotal'        => $totalBonusIncome,
            'withdrawnTotal'    => $totalWithdrawn,
            
            // Data Jaringan
            'totalMembers'      => $totalMembers,
            'networkStats'      => $networkStats
        ]);
    }
}