<?php

namespace App\Livewire\Admin\BusinessesUsers;

use App\Models\User;
use Livewire\Component;
use App\Models\Business;
use Livewire\WithPagination;
use App\Models\BusinessesUsers;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $user_id;
    public $businesses_id;
    public $businesses_user_id;
    public $businesses;
    public $users;
    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Businesses Users Management";

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->businesses = Business::all();
        $this->users = User::all();
    }

    public function render()
    {
       $businessesUsers = BusinessesUsers::search($this->search)
                      ->latest()
                      ->paginate($this->perPage);

        return view('livewire.admin.businesses-users.index', [
            'businessesUsers' => $businessesUsers,
            'businesses' => $this->businesses,
            'users' => $this->users,
        ]);
    }

    public function openModal($id = null)
    {
        $this->resetInput();
        if ($id) {
            $businessesUser = BusinessesUsers::findOrFail($id);
            $this->businesses_user_id = $businessesUser->id;
            $this->businesses_id = $businessesUser->businesses_id;
            $this->user_id = $businessesUser->user_id;
        }
        $this->isOpen = true;
        
    }

    public function closeModal()
    {
       
        $this->isOpen = false;
    }

    private function resetInput()
    {
        $this->user_id = '';
        $this->businesses_id = '';
        $this->businesses_user_id = null;
    }

    public function store()
    {
        $this->validate($this->rules());

        $businessesUser = BusinessesUsers::updateOrCreate(
            ['id' => $this->businesses_user_id],
            $this->formData()
        );

        $this->afterSave($businessesUser->wasRecentlyCreated);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        BusinessesUsers::findOrFail($this->confirmingDelete)->delete();

        $this->dispatch('success', [
            'type' => 'success',
            'message' => 'Businesses User berhasil dihapus!',
        ]);
    }

    protected function rules()
    {
        return [
           'user_id' => 'required|integer',
           'businesses_id' => 'required|integer',
        ];
    }

    protected function formData()
    {
        return [
            'user_id' => $this->user_id,
            'businesses_id' => $this->businesses_id,
        ];
    }

    protected function afterSave($created)
    {
        $this->closeModal();
        $this->resetInput();

        $message = $created
            ? 'Businesses User berhasil ditambahkan!'
            : 'Businesses User berhasil diupdate!';

        $this->dispatch('success', [
            'type' => 'success',
            'message' => $message,
        ]);
    }
}
