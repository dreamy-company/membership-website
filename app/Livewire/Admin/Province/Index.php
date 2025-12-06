<?php

namespace App\Livewire\Admin\Province;

use App\Models\Province;
use Livewire\Component;

class Index extends Component
{
    use \Livewire\WithPagination;
    public $title = 'Provinces';
    public $search = '';
    public $page = 1;
    public $totalPages = 10;
    public $perPage = 10; // Default pagination
    public $name, $province_id;
    public $isOpen = false;

    public function updatingPerPage()
    {
        $this->resetPage(); // reset to page 1 whenever per page count is changed
    }

    public function gotoPage($num)
    {
        $this->page = $num;
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

        session()->flash('message', $this->province_id ? 'Province updated.' : 'Province created.');
        $this->closeModal();
        $this->resetInput();
    }

    public function delete($id)
    {
        Province::find($id)->delete();
        session()->flash('message', 'Province deleted.');
    }
}
