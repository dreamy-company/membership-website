<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusLevelSetup extends Model
{
    protected $guarded = ['id'];

    // ini function search yang ada di livewire
    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->where('kodeBonus', 'like', "%{$term}%")
                ->orWhere('persenBonus', 'like', "%{$term}%");
        }
    }

}
