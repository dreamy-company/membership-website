<?php

namespace App\Livewire\Members\Withdrawals;


use Livewire\Component;
use Livewire\WithPagination;

// Models
use App\Models\Bonus;
use App\Models\Withdrawal;

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
        $withdrawals = Withdrawal::search($this->search)->where('member_id', auth()->user()->id)
            ->latest()
            ->paginate($this->perPage);

        $bonusTotal = Bonus::where('member_id', auth()->user()->id)->first();

        return view('livewire.members.withdrawals.index', compact('withdrawals', 'bonusTotal'));
    }
}
