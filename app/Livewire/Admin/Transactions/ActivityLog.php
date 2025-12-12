<?php

namespace App\Livewire\Admin\Transactions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityLog as ActivityLogModel;

class ActivityLog extends Component
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
                      ->paginate($this->perPage);
        return view('livewire.admin.transactions.activity-log', compact('activities'));
    }
}
