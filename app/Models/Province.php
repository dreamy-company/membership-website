<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $guarded = ['id'];

    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%");
        }
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

}
