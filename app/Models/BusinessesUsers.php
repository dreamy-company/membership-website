<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessesUsers extends Model
{
    protected $guarded = ['id'];

    // ini function search yang ada di livewire
    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->whereHas('user', function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%");
            })
            ->orWhereHas('business', function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%");
            });
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'businesses_id');
    }
}
