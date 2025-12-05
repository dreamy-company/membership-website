<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Provinces extends Component
{
    public function render()
    {
        return view('livewire.admin.provinces',[
            'provinces' => \App\Models\Province::all(),
            'name' => 'Provinces Management',
        ]);
    }
}
