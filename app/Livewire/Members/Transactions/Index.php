<?php

namespace App\Livewire\Members\Transactions;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Province;
use App\Models\Transaction;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $name;
    public $province_id;
    public $isOpen = false;
    public $confirmingDelete;
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

        return view('livewire.members.transactions.index', compact('transactions'));
    }
}
