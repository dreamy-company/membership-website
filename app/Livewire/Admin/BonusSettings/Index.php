<?php

namespace App\Livewire\Admin\BonusSettings;

use App\Models\BonusSetting;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // 1. Hapus 'public $settings;' agar tidak bentrok dengan render
    public $search = '';
    
    // 2. Sesuaikan Property dengan Kolom Database Baru
    public $setting_id;
    public $name;
    public $level;
    public $percentage;
    public $is_active = true;

    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Bonus Level Settings";

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // 3. Query disesuaikan (Cari berdasarkan Level, bukan member)
        $query = BonusSetting::query();

        if ($this->search) {
            $query->where('level', 'like', '%' . $this->search . '%')
                  ->orWhere('percentage', 'like', '%' . $this->search . '%');
        }

        // Urutkan berdasarkan Level (1, 2, 3...) biar rapi
        $settings = $query->orderBy('level', 'asc')
                          ->paginate($this->perPage);

        return view('livewire.admin.bonus-settings.index', compact('settings'));
    }

    public function openModal($id = null)
    {
        $this->resetInput();
        
        if ($id) {
            $setting = BonusSetting::findOrFail($id);
            $this->setting_id = $setting->id;
            $this->level = $setting->level;
            $this->percentage = $setting->percentage;
            $this->is_active = $setting->is_active;
        }
        
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetErrorBag(); // Hapus pesan error validasi sebelumnya
    }

    private function resetInput()
    {
        $this->setting_id = null;
        $this->level = '';
        $this->percentage = '';
        $this->is_active = true;
    }

    public function store()
    {
        $this->validate($this->rules());

        $bonus = BonusSetting::updateOrCreate(
            ['id' => $this->setting_id],
            $this->formData()
        );

        $this->afterSave($bonus->wasRecentlyCreated);
    }

    // Fitur Toggle Status Aktif/Non-Aktif (Opsional tapi berguna)
    public function toggleStatus($id)
    {
        $setting = BonusSetting::find($id);
        if($setting) {
            $setting->update(['is_active' => !$setting->is_active]);
            $this->dispatch('success', ['type' => 'success', 'message' => 'Status berhasil diubah!']);
        }
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
        $this->dispatch('show-delete-confirmation'); // Pastikan ada listener di view/js
    }

    public function delete()
    {
        $setting = BonusSetting::find($this->confirmingDelete);
        
        if ($setting) {
            $setting->delete();
            $this->dispatch('success', [
                'type' => 'success', 
                'message' => 'Setting Level berhasil dihapus!'
            ]);
        }
        
        $this->confirmingDelete = null;
    }

    protected function rules()
    {
        // Validasi Level harus unique, kecuali sedang edit data sendiri
        return [
            'level'      => 'required|integer|min:1|unique:bonus_settings,level,' . $this->setting_id,
            'percentage' => 'required|numeric|min:0|max:100',
        ];
    }

    protected function formData()
    {
        return [
            'level'      => $this->level,
            'percentage' => $this->percentage,
            'is_active'  => $this->is_active ?? true,
        ];
    }

    protected function afterSave($created)
    {
        $this->closeModal();
        $this->resetInput();

        $message = $created
            ? 'Level baru berhasil ditambahkan!'
            : 'Persentase berhasil diupdate!';

        $this->dispatch('success', [
            'type' => 'success',
            'message' => $message,
        ]);
    }
}