<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domicile extends Model
{
    protected $guarded = ['id'];
    protected $table = 'domiciles';

    // ini function search yang ada di livewire
    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhereHas('province', function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%");
                });
        }
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
