<?php

namespace App\Livewire\Members\Dashboard;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction;
use App\Models\Bonus;
use App\Models\Member;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Member Dashboard";

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $transactions = Transaction::search($this->search)->where('member_id', auth()->user()->id)
            ->latest()
            ->paginate($this->perPage);

        $transactionTotal = Transaction::where('member_id', auth()->user()->id)->sum('amount');
        $bonusTotal = Bonus::where('member_id', auth()->user()->id)->first();
        $totalMembers = Member::where('parent_user_id', auth()->user()->id)->count();

        return view('dashboard', compact('transactions', 'transactionTotal', 'bonusTotal', 'totalMembers'));
    }
}
