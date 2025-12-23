<?php

namespace App\Livewire\Admin\Domicilies;

use Livewire\Component;
use App\Models\Domicile;
use App\Models\Province;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $name;
    public $code;
    public $domicile_id;
    public $province_id;
    public $provinces;
    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Domicile Management";

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->provinces = Province::all();
    }

    public function render()
    {
        $domiciles = Domicile::search($this->search)
                      ->latest()
                      ->paginate($this->perPage);
        return view('livewire.admin.domiciles.index', compact('domiciles'));
    }

    public function openModal($id = null)
    {
        $this->resetInput();
        if ($id) {
            $domicile = Domicile::findOrFail($id);
            $this->domicile_id = $domicile->id;
            $this->province_id = $domicile->province_id;
            $this->code = $domicile->code;
            $this->name = $domicile->name;
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
        $this->province_id = '';
        $this->code = '';
        $this->domicile_id = null;
    }

    public function store()
    {
        $this->validate($this->rules());

        $domicile = Domicile::updateOrCreate(
            ['id' => $this->domicile_id],
            $this->formData()
        );

        $this->afterSave($domicile->wasRecentlyCreated);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        Domicile::findOrFail($this->confirmingDelete)->delete();

        $this->dispatch('success', [
            'type' => 'success',
            'message' => 'Domicile berhasil dihapus!',
        ]);
    }

    protected function rules()
    {
        return [
            'code'    => 'required|string|max:3|unique:domiciles,code,' . $this->domicile_id,
            'name'    => 'required|string|unique:domiciles,name,' . $this->domicile_id,
            'province_id' => 'required|integer|exists:provinces,id',
        ];
    }

    protected function formData()
    {
        return [
            'code'    => $this->code,
            'name'    => $this->name,
            'province_id' => $this->province_id,
        ];
    }

    protected function afterSave($created)
    {
        $this->closeModal();
        $this->resetInput();

        $message = $created
            ? 'Domicile berhasil ditambahkan!'
            : 'Domicile berhasil diupdate!';

        $this->dispatch('success', [
            'type' => 'success',
            'message' => $message,
        ]);
    }
}
