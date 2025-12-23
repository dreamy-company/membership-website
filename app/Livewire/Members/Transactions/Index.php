<?php

namespace App\Livewire\Members\Transactions;


use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Transaction;

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
       $transactions = Transaction::search($this->search)->where('member_id', auth()->user()->id)
                      ->latest()
                      ->paginate($this->perPage);

        $transactionTotal = Transaction::where('member_id', auth()->user()->id)->sum('amount');

        return view('livewire.members.transactions.index', compact('transactions', 'transactionTotal'));
    }
}
