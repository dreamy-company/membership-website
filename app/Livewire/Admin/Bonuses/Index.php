<?php

namespace App\Livewire\Admin\Bonuses;

use App\Models\Bonus;
use App\Models\Member;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $bonus_id;
    public $member_id;
    public $balance;
    public $members;
    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Bonus Management";

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->members = Member::with('user')->get();
    }

    public function render()
    {
       $bonuses = Bonus::search($this->search)
                      ->latest()
                      ->paginate($this->perPage);

        return view('livewire.admin.bonuses.index', compact('bonuses'));
    }

    public function openModal($id = null)
    {
        $this->resetInput();
        if ($id) {
            $bonus = Bonus::findOrFail($id);
            $this->bonus_id = $bonus->id;
            $this->member_id = $bonus->member_id;
            $this->balance = $bonus->balance;
        }
        $this->isOpen = true;
        
    }

    public function closeModal()
    {
       
        $this->isOpen = false;
    }

    private function resetInput()
    {
        $this->member_id = '';
        $this->balance = '';
        $this->bonus_id = null;
    }

    public function store()
    {
        $this->validate($this->rules());

        $bonus = Bonus::updateOrCreate(
            ['id' => $this->bonus_id],
            $this->formData()
        );

        $this->afterSave($bonus->wasRecentlyCreated);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        Bonus::findOrFail($this->confirmingDelete)->delete();

        $this->dispatch('success', [
            'type' => 'success',
            'message' => 'Bonus berhasil dihapus!',
        ]);
    }

    protected function rules()
    {
        return [
            'member_id' => 'required|unique:bonuses,member_id,' . $this->bonus_id,
            'balance'   => 'required|numeric',
        ];
    }

    protected function formData()
    {
        return [
            'member_id'    => $this->member_id,
            'balance' => $this->balance,
        ];
    }

    protected function afterSave($created)
    {
        $this->closeModal();
        $this->resetInput();

        $message = $created
            ? 'Bonus berhasil ditambahkan!'
            : 'Bonus berhasil diupdate!';

        $this->dispatch('success', [
            'type' => 'success',
            'message' => $message,
        ]);
    }
}
