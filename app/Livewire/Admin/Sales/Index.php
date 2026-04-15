<?php

namespace App\Livewire\Admin\Sales;

use App\Models\Business;
use App\Models\Sale;
use Livewire\WithPagination;
use Livewire\Component;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $start_date;
    public $end_date;
    public $filter_umkm = '';
    public $perPage = 10;
    public $title = 'Sales Management';

    public $businesses;

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->businesses = Business::orderBy('name')->get();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'start_date', 'end_date', 'filter_umkm'])) {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'start_date', 'end_date', 'filter_umkm']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Sale::with(['business', 'transaction.member.user'])->search($this->search);

        if ($this->start_date && $this->end_date) {
            $query->whereBetween('SalesDate', [$this->start_date, $this->end_date]);
        } elseif ($this->start_date) {
            $query->whereDate('SalesDate', '>=', $this->start_date);
        } elseif ($this->end_date) {
            $query->whereDate('SalesDate', '<=', $this->end_date);
        }

        if (!empty($this->filter_umkm)) {
            $query->where('IDBisnis', $this->filter_umkm);
        }

        $sales = $query->orderByDesc('SalesDate')->orderByDesc('ID')->paginate($this->perPage);

        return view('livewire.admin.sales.index', compact('sales'));
    }
}
