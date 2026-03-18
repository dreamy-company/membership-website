<?php

namespace App\Livewire\Members\Withdrawals;


use App\Models\Bonus;
use App\Models\Withdrawal;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $title = "Withdrawals";
    
    public $isOpen = false;

    public $payment_receipt;


    public function openModal($path){
        $this->isOpen = true;
        $this->payment_receipt = $path;
    }

    public function closeModal(){
        $this->isOpen = false;
        $this->payment_receipt = null;
    }


    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function mount()
    {

    }

    public function render()
    {
        $user_id = auth()->user();

        $withdrawals = Withdrawal::search($this->search)->where('member_id', $user_id->member->id)
            ->latest()
            ->paginate($this->perPage);

        $bonusTotal = Bonus::where('member_id', $user_id->member->id)->first();

        return view('livewire.members.withdrawals.index', compact('withdrawals', 'bonusTotal'));
    }

    public function printMemberReceipt($id)
    {
        // 1. KUNCI KEAMANAN: Hanya ambil data penarikan milik member yang sedang login!
        $withdrawal = Withdrawal::where('id', $id)
            ->where('member_id', auth()->user()->member->id) 
            ->firstOrFail(); // Jika bukan miliknya, otomatis muncul 404 Not Found

        // 2. Load ke view PDF khusus member (kita buat view-nya di langkah 3)
        $pdf = Pdf::loadView('pdf.member-receipt', compact('withdrawal'));

        // 3. Tampilkan PDF di browser (atau gunakan download() jika ingin otomatis terunduh)
        return $pdf->stream('Bukti_Penarikan_Saldo_' . $withdrawal->date . '.pdf');
    }
}
