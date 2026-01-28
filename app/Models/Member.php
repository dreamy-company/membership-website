<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Member extends Model
{
    protected $guarded = ['id'];

    // ini function search yang ada di livewire
    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->where('address', 'like', "%{$term}%")
                ->orWhere('phone_number', 'like', "%{$term}%")
                ->orWhereHas('user', function($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%");
                });
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function domicile()
    {
        return $this->belongsTo(Domicile::class);
    }

    public function parentUser()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function bonus()
    {
        return $this->hasOne(Bonus::class, 'member_id');
    }

    public function bonusLogs()
    {
        return $this->hasMany(BonusLog::class, 'member_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'member_id');
    }

    // --- VIRTUAL ATTRIBUTE (ACCESSOR) ---
    // Ini yang membuat $member->balance bisa dipakai meski tidak ada di database
    protected function balance(): Attribute
    {
        return Attribute::make(
            get: function () {
                // 1. Total Uang Masuk (Semua Bonus Downline + Bonus Pribadi)
                $income = $this->bonusLogs()->sum('amount');

                // 2. Total Uang Keluar (Withdrawal yang sudah disetujui)
                $expense = $this->withdrawals()->sum('amount');

                // 3. Kembalikan Sisa Saldo
                return $income - $expense;
            }
        );
    }
   
}
