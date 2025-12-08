<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = ['id'];

    // ini function search yang ada di livewire
   public function scopeSearch($query, $term)
    {
        if ($term) {
            $term = "%{$term}%";

            $query->where('transaction_code', 'like', $term)
                ->orWhere('transaction_date', 'like', $term)
                ->orWhereHas('business', function ($q) use ($term) {
                    $q->where('name', 'like', $term);
                })
                ->orWhereHas('member.user', function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%");
                });
        }
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'businesses_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
            
}
