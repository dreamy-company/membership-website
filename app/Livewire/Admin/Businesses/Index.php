<?php

namespace App\Livewire\Admin\Businesses;

use App\Models\Business;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{ 
    use WithPagination;

    public $search = '';
    public $name;
    public $address;
    public $phone;
    public $business_id;
    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Business Management";

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
       $businesses = Business::search($this->search)
                      ->latest()
                      ->paginate($this->perPage);

        return view('livewire.admin.businesses.index', compact('businesses'));
    }

    public function openModal($id = null)
    {
        $this->resetInput();
        if ($id) {
            $business = Business::findOrFail($id);
            $this->business_id = $business->id;
            $this->name = $business->name;
            $this->address = $business->address;
            $this->phone = $business->phone;
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
        $this->address = '';
        $this->phone = '';
        $this->business_id = null;
    }

    public function store()
    {
        $this->validate($this->rules());

        $business = Business::updateOrCreate(
            ['id' => $this->business_id],
            $this->formData()
        );

        $this->afterSave($business->wasRecentlyCreated);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        Business::findOrFail($this->confirmingDelete)->delete();

        $this->dispatch('success', [
            'type' => 'success',
            'message' => 'Business berhasil dihapus!',
        ]);
    }

    protected function rules()
    {
        return [
            'name'    => 'required|string|unique:businesses,name,' . $this->business_id,
            'address' => 'required|string',
            'phone'   => 'required|min:10|max:20',
        ];
    }

    protected function formData()
    {
        return [
            'name'    => $this->name,
            'address' => $this->address,
            'phone'   => $this->phone,
        ];
    }

    protected function afterSave($created)
    {
        $this->closeModal();
        $this->resetInput();

        $message = $created
            ? 'Business berhasil ditambahkan!'
            : 'Business berhasil diupdate!';

        $this->dispatch('success', [
            'type' => 'success',
            'message' => $message,
        ]);
    }



}
