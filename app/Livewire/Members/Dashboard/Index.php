<?php

namespace App\Livewire\Members\Dashboard;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Province;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $name;
    public $province_id;
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
        $provinces = Province::when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate($this->perPage);

        return view('dashboard', compact('provinces'));
    }

    public function openModal($id = null)
    {
        $this->resetInput();
        if ($id) {
            $province = Province::findOrFail($id);
            $this->province_id = $province->id;
            $this->name = $province->name;
        }
        $this->isOpen = true;
    }

    public function closeModal()
    {

        $this->isOpen = false;
    }

    private function resetInput()
    {
        $this->name = '';
        $this->province_id = null;
    }

    public function store()
    {
        $this->validate(['name' => 'required|string|unique:provinces,name,' . $this->province_id]);

        Province::updateOrCreate(['id' => $this->province_id], ['name' => $this->name]);

        $this->closeModal();
        $this->resetInput();

        $this->dispatch('success', [
            'type' => 'success',
            'message' => $this->province_id ? 'Provinsi berhasil diupdate!' : 'Provinsi berhasil ditambahkan!',
        ]);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        Province::findOrFail($this->confirmingDelete)->delete();

        $this->dispatch('success', [
            'type' => 'success',
            'message' => 'Provinsi berhasil dihapus!',
        ]);
    }
}
