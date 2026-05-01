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
        $memberInfo = $this->sales->first() ? $this->sales->first()->member : null;
        $totalBelanja = $this->sales->sum('TotalCost');

        return view('livewire.members.transactions.detail', [
            'memberInfo' => $memberInfo,
            'totalBelanja' => $totalBelanja,
        ]);
    }
}
