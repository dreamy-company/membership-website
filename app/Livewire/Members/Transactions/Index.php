<?php

namespace App\Livewire\Members\Transactions;

use App\Models\Transaction;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $name;
    public $perPage = 10;
    public $title = "Transactions";
    public $isDetailModalOpen = false;
    public $salesDetails = [];
    public $selectedTransactionCode;

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        // Reset paginasi ke halaman 1 ketika user mengetik di kolom pencarian
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
        // 1. OPTIMASI STATISTIK DOMPET (1 Query)
        // ==========================================
        // Menggabungkan 2 query SUM menjadi 1 kali jalan ke database
        $txStats = Transaction::where('member_id', $myMemberId)
            ->selectRaw("SUM(bonus) as total_income")
            ->selectRaw("SUM(amount) as total_omzet")
            ->first();

        $totalIncome = $txStats->total_income ?? 0;
        $totalOmzet  = $txStats->total_omzet ?? 0;
        $totalWithdrawal = Withdrawal::where('member_id', $myMemberId)->sum('amount');

        // ==========================================
        // 2. DATA TRANSAKSI (HEMAT MEMORI RAM)
        // ==========================================
        $transactionsQuery = Transaction::with(['sourceMember.user:id,name'])
            // HANYA ambil kolom yang ditampilkan di tabel agar memori tidak penuh
            ->select('id', 'member_id', 'transaction_id', 'transaction_code', 'LevelMember', 'BonusPercent', 'bonus', 'created_at')
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
        // 3. DATA WITHDRAWAL (HEMAT MEMORI RAM)
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
        // 4. GABUNGKAN, URUTKAN, & PAGINASI MANUAL
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

        return view('livewire.members.transactions.index', [
            'transactions'    => $paginatedLogs, 
            'totalIncome'     => $totalIncome,
            'totalOmzet'      => $totalOmzet,
            'totalWithdrawal' => $totalWithdrawal 
        ]);
    }

    public function showDetail($transactionCode, $sourceName)
    {
        $loggedInUserName = auth()->user()->name;

        if ($loggedInUserName === $sourceName) {
            return redirect()->route('member.transactions.detail', ['transactionCode' => $transactionCode]);
        } else {
            // Optionally, you can dispatch an event to show a notification
            $this->dispatch('show-notification', ['message' => 'You can only view details of your own transactions.']);
        }
    }

    public function closeDetailModal()
    {
        $this->isDetailModalOpen = false;
        $this->salesDetails = [];
        $this->selectedTransactionCode = null;
    }
}