<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Member;
use Livewire\Component;

class Index extends Component
{

    public $title = "Admin Dashboard";
    public $transactionsCount, $membersCount, $businessesCount, $provincesCount, $usersCount;
    protected $queryString = ['search' => ['except' => '']];
    public $perPage = 10;
    public $search = '';

    public function mount()
    {
        $this->transactionsCount = \App\Models\Transaction::count();
        $this->membersCount = \App\Models\Member::count();
        $this->businessesCount = \App\Models\Business::count();
        $this->provincesCount = \App\Models\Province::count();
        $this->usersCount = \App\Models\User::count();
    }

//    public function updatingSearch()
//     {
//         $this->resetPage();
//     }

    public function render()
    {
        $members = Member::search($this->search)
        ->latest()
        ->paginate($this->perPage);

        // kalau search kosong, balikin data kosong
        if ($this->search === '') {
            $members = collect([]); // ← aman, bukan null
        }
        

        return view('livewire.admin.dashboard.index', compact('members'));
    }
}
