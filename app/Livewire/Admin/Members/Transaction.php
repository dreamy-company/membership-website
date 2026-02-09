<?php

namespace App\Livewire\Admin\Members;

use App\Models\Member;
use Livewire\Component;
use App\Models\Withdrawal;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Transaction as TransactionModel;

class Transaction extends Component
{
    use WithPagination;

    public $memberId;
    public $member; // Menyimpan object Member
    
    // Search & Pagination
    public $search = '';
    public $perPage = 10;
    protected $paginationTheme = 'tailwind';

    // UI State
    public $isOpen = false;
    
    // Menerima ID dari Route
    public function mount($id)
    {
        $this->memberId = $id;
        
        $this->member = Member::with('user')->findOrFail($id);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // 1. QUERY DATA TRANSAKSI (Khusus Member Ini)
        $transactions = TransactionModel::with(['sourceMember.user', 'business'])
            ->where('member_id', $this->memberId)
            ->where(function (Builder $query) {
                if ($this->search) {
                    $query->whereHas('sourceMember.user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('transaction_code', 'like', '%' . $this->search . '%')
                    ->orWhere('LevelMember', 'like', '%' . $this->search . '%');
                }
            })
            ->latest()
            ->paginate($this->perPage);

        // 2. HITUNG TOTAL BONUS (INCOME)
        // Total semua bonus yang pernah masuk ke member ini
        $totalBonusIncome = TransactionModel::where('member_id', $this->memberId)->sum('bonus');

        // 3. HITUNG TOTAL PENARIKAN (WITHDRAWAL)
        // Ambil dari tabel withdrawals (Asumsi status 'approved' atau semua)
        // $totalWithdrawn = Withdrawal::where('member_id', $this->memberId)
        //                     ->where('status', 'approved') // Opsional: Hapus jika ingin menghitung semua request
        //                     ->sum('amount');

        $totalWithdrawn = 0;

        // 4. HITUNG SALDO SAAT INI
        $currentBalance = $totalBonusIncome - $totalWithdrawn;

        return view('livewire.admin.members.transaction', [
            'transactions'   => $transactions,
            'totalBonus'     => $totalBonusIncome,
            'totalWithdrawn' => $totalWithdrawn,
            'currentBalance' => $currentBalance
        ]);
    }

    public function redirectToDetail($id)
    {
        // Arahkan ke route yang sudah kita definisikan sebelumnya
        return redirect()->route('members.transaction.detail', ['id' => $id]);
    }
}
