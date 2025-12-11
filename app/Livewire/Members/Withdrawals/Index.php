<?php

namespace App\Livewire\Members\Withdrawals;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Withdrawal;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $title = "Withdrawals";


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

        return view('livewire.members.withdrawals.index', compact('withdrawals'));
    }
}
