<?php

namespace App\Livewire\Admin\Province;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Province;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $province_id;
    public $code;
    public $name;
    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Province Management";

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
       $provinces = Province::search($this->search)
                      ->latest()
                      ->paginate($this->perPage);

        return view('livewire.admin.province.index', compact('provinces'));
    }

    public function openModal($id = null)
    {
        $this->resetInput();
        if ($id) {
            $province = Province::findOrFail($id);
            $this->province_id = $province->id;
            $this->code = $province->code;
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
        $this->code = '';
        $this->name = '';
        $this->province_id = null;
    }

    public function store()
    {
        $this->validate($this->rules());

        $province = Province::updateOrCreate(
            ['id' => $this->province_id],
            $this->formData()
        );

        $this->afterSave($province->wasRecentlyCreated);
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
            'message' => 'Province berhasil dihapus!',
        ]);
    }

    protected function rules()
    {
        return [
            'code'    => 'required|string|max:10',
            'name' => 'required|string|max:255',
        ];
    }

    protected function formData()
    {
        return [
            'code'    => $this->code,
            'name' => $this->name,
        ];
    }

    protected function afterSave($created)
    {
        $this->closeModal();
        $this->resetInput();

        $message = $created
            ? 'Province berhasil ditambahkan!'
            : 'Province berhasil diupdate!';

        $this->dispatch('success', [
            'type' => 'success',
            'message' => $message,
        ]);
    }
}
