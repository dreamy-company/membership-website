<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $guarded = ['id'];

    // ini function search yang ada di livewire
    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('address', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%");
        }
    }
}
