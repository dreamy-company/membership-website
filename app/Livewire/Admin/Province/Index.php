<?php

namespace App\Livewire\Admin\Province;

use Livewire\Component;
use App\Models\Province;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public $title = 'Provinces';
    public $search = '';
    public $perPage = 10; // Default pagination
    public $name, $province_id;
    public $isOpen = false;
    public $confirmingDelete;

    public function updatingPerPage()
    {
        $this->resetPage(); // reset to page 1 whenever per page count is changed
    }

    protected $queryString = [
        'search' => ['except' => '']
    ];

    public function getProvinces()
    {
        return Province::when($this->search, function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%');
        })->paginate($this->perPage);
    }


    public function render()
    {
        return view('livewire.admin.province.index',[
            'title' => $this->title,
            'provinces' => $this->getProvinces(),
        ]);
    }


    public function openModal($id = null)
    {
        $this->resetInput();
        if($id) {
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
        $this->validate([
            'name' => 'required|string|unique:provinces,name,' . $this->province_id,
        ]);

        Province::updateOrCreate(
            ['id' => $this->province_id],
            ['name' => $this->name]
        );

        
        $this->closeModal();
        $this->resetInput();
        $this->dispatch('swal:success', [
            'type' => 'success',
            'message' => $this->province_id ? 'Provinsi berhasil diupdate!' : 'Provinsi berhasil ditambahkan!'
     ]);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        Province::find($this->confirmingDelete)->delete();
   
         $this->dispatch('swal:success', [
            'type' => 'success',
            'message' => 'Provinsi berhasil dihapus!'
        ]);
    }
}
