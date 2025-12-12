<?php

namespace App\Livewire\Business\Transactions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityLog as ActivityLogModel;

class ActivyLog extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $title = "Transaction Management";
    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $activities = ActivityLogModel::search($this->search)
                      ->latest()
                      ->where('user_id', auth()->id())
                      ->paginate($this->perPage);
        return view('livewire.business.transactions.activy-log', compact('activities'));
    }
}
