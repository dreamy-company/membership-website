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
        // 1. DATA TABEL (LIST HISTORY TRANSAKSI)
        // ==========================================
        // Kita ambil dari tabel Transaction dimana member_id = Saya
        // Ini akan menampilkan:
        // - Row 'Leader' (Saat saya belanja sendiri)
        // - Row 'Level 1' (Bonus belanja sendiri)
        // - Row 'Level 2', 'Level 3' (Bonus dari Downline)
        
        $transactions = Transaction::with(['sourceMember.user', 'business'])
            ->where('member_id', $myMemberId)
            ->where(function (Builder $query) {
                if ($this->search) {
                    $query->whereHas('sourceMember.user', function ($q) {
                        // Cari berdasarkan nama orang yang belanja (Sumber Bonus)
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('transaction_code', 'like', '%' . $this->search . '%')
                    ->orWhere('LevelMember', 'like', '%' . $this->search . '%'); // Bisa cari 'Level 1', 'Leader'
                }
            })
            ->latest()
            ->paginate($this->perPage);

        // ==========================================
        // 2. STATISTIK KEUANGAN
        // ==========================================
        
        // A. Total Belanja Pribadi (My Spending)
        // Logika: Jumlahkan 'amount' TAPI hanya yang 'Leader'.
        // Kenapa? Karena saat belanja, kita dapat row 'Leader' dan 'Level 1'.
        // Kalau tidak difilter, total belanja akan terhitung 2x lipat.
        $mySpendingTotal = Transaction::where('member_id', $myMemberId)
                            ->where('LevelMember', 'Leader') 
                            ->sum('amount');
        
        // B. Total Semua Bonus Masuk (Income)
        // Jumlahkan kolom 'bonus'. Ini adalah uang real yang masuk ke dompet.
        $totalBonusIncome = Transaction::where('member_id', $myMemberId)->sum('bonus'); 

        // C. Total Uang Ditarik (Withdrawal)
        // [REQUEST] Kosongkan dulu
        $totalWithdrawn = 0; 

        // ==========================================
        // 3. STATISTIK JARINGAN (NETWORK)
        // ==========================================
        // (Logic Tree/Downline tetap sama)
        $networkStats = $myMember->getNetworkStats(5); 
        $totalMembers = array_sum($networkStats);      

        return view('dashboard', [
            // Data Tabel
            'transactions'      => $transactions,
            
            // Data Keuangan
            'transactionTotal'  => $mySpendingTotal,
            'bonusTotal'        => $totalBonusIncome,
            'withdrawnTotal'    => $totalWithdrawn,
            
            // Hitung Saldo Saat Ini (Bonus Masuk - 0)
            'currentBalance'    => $totalBonusIncome - $totalWithdrawn, 
            
            // Data Jaringan
            'totalMembers'      => $totalMembers,
            'networkStats'      => $networkStats
        ]);
    }
}