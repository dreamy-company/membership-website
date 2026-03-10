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

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function mount()
    {

    }

    public function render()
    {
        $myMemberId = auth()->user()->member->id;

        // ==========================================
        // 1. AMBIL DATA TRANSAKSI (BONUS MASUK)
        // ==========================================
        $transactionsQuery = Transaction::with(['sourceMember.user', 'business'])
            ->where('member_id', $myMemberId);

        if ($this->search) {
            $transactionsQuery->where(function (Builder $query) {
                $query->whereHas('sourceMember.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('business', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('transaction_code', 'like', '%' . $this->search . '%')
                ->orWhere('LevelMember', 'like', '%' . $this->search . '%'); 
            });
        }

        $transactions = $transactionsQuery->get()->map(function ($item) {
            $item->log_type = 'bonus'; // Tandai baris ini sebagai Bonus
            return $item;
        });


        // ==========================================
        // 2. AMBIL DATA WITHDRAWAL (UANG KELUAR)
        // ==========================================
        $withdrawalsQuery = Withdrawal::where('member_id', $myMemberId);

        if ($this->search) {
            // Jika ada pencarian nominal di withdrawal
            $withdrawalsQuery->where('amount', 'like', '%' . $this->search . '%');
        }

        $withdrawals = $withdrawalsQuery->get()->map(function ($item) {
            $item->log_type = 'withdrawal'; // Tandai baris ini sebagai Withdrawal
            $item->created_at = $item->date; // Samakan format tanggal agar bisa diurutkan bersamaan
            return $item;
        });


        // ==========================================
        // 3. GABUNGKAN, URUTKAN, & PAGINASI MANUAL
        // ==========================================
        // Gabungkan (concat) lalu urutkan dari yang terbaru (sortByDesc)
        $allLogs = $transactions->concat($withdrawals)->sortByDesc('created_at')->values();

        // Buat pagination manual
        $page = Paginator::resolveCurrentPage() ?: 1;
        $paginatedLogs = new LengthAwarePaginator(
            $allLogs->forPage($page, $this->perPage), // Data per halaman
            $allLogs->count(), // Total data
            $this->perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()] // URL untuk klik next page
        );


        // ==========================================
        // 4. HITUNG STATISTIK DOMPET
        // ==========================================
        $totalIncome = Transaction::where('member_id', $myMemberId)->sum('bonus');
        $totalOmzet = Transaction::where('member_id', $myMemberId)->sum('amount');
        $totalWithdrawal = Withdrawal::where('member_id', $myMemberId)->sum('amount');

        return view('livewire.members.transactions.index', [
            'transactions' => $paginatedLogs, // Kita lempar variabel yang sudah digabung
            'totalIncome'  => $totalIncome,
            'totalOmzet'   => $totalOmzet,
            'totalWithdrawal' => $totalWithdrawal // Tambahan untuk di view (opsional)
        ]);
    }
}
