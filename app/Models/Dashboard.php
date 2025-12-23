<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    protected $guarded = [];

    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->where(function($q) use ($term) {
                $q->where('address', 'like', "%{$term}%")
                ->orWhere('phone_number', 'like', "%{$term}%")
                ->orWhereHas('user', function($q2) use ($term) {
                        $q2->where('name', 'like', "%{$term}%");
                });
            });
        }
    }


}
