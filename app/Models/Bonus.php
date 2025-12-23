<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    protected $guarded = ['id'];

    // ini function search yang ada di livewire
    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->where('balance', 'like', "%{$term}%")
                ->orWhereHas('member.user', function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%");
                });
        }
    }


    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
