<?php

namespace App\Livewire\Members\Transactions;

use App\Models\Sale;
use Livewire\Component;

class Detail extends Component
{
    public $transactionCode;
    public $sales;

    public function mount($transactionCode)
    {
        $this->transactionCode = $transactionCode;
        $this->sales = Sale::where('SalesNumber', $this->transactionCode)->get();
    }

    public function render()
    {
        return view('livewire.members.transactions.detail');
    }
}
