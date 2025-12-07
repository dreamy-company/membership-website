<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $guarded = ['id'];

    // ini function search yang ada di livewire
    public function scopeSearch($query, $term)
    {
         if ($term) {
            $term = "%{$term}%";

            $query->where('withdrawal_amount', 'like', $term)
                ->orWhere('payment_receipt', 'like', $term)
                ->orWhereHas('member.user', function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%");
                });
        }
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
