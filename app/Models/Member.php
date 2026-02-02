<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

class Member extends Model
{
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    
    public function scopeSearch(Builder $query, $term)
    {
        if ($term) {
            $query->where('address', 'like', "%{$term}%")
                ->orWhere('phone_number', 'like', "%{$term}%")
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$term}%"));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (Virtual Attributes)
    |--------------------------------------------------------------------------
    */

    /**
     * Menghitung Sisa Saldo (Income - Expense)
     * $member->balance
     */
    protected function balance(): Attribute
    {
        return Attribute::make(
            get: function () {
                // 1. Total Uang Masuk (Bonus Downline + Cashback Pribadi)
                $income = $this->bonusLogs()->sum('amount');

                // 2. Total Uang Keluar (HANYA Withdrawal yang APPROVED)
                $expense = $this->withdrawals()
                                ->where('status', 'approved') // [FIX] Wajib filter status
                                ->sum('amount');

                return $income - $expense;
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC (Network & MLM)
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan total jumlah member di bawah jaringan (hingga maxDepth).
     */
    public function getDownlineCount($maxDepth = 5)
    {
        $tree = $this->fetchDownlineTree($maxDepth);
        
        // Ratakan array multi-level menjadi satu array ID, lalu hitung totalnya
        $allIds = [];
        foreach ($tree as $levelIds) {
            $allIds = array_merge($allIds, $levelIds);
        }

        return count(array_unique($allIds));
    }

    /**
     * Mendapatkan statistik member per level.
     * Return format: [1 => 5, 2 => 10, 3 => 0, ...]
     */
    public function getNetworkStats($maxDepth = 5)
    {
        $tree = $this->fetchDownlineTree($maxDepth);
        $stats = [];

        for ($i = 1; $i <= $maxDepth; $i++) {
            $stats[$i] = isset($tree[$i]) ? count($tree[$i]) : 0;
        }

        return $stats;
    }

    /**
     * Core Logic: Crawling jaringan MLM.
     * Mengembalikan array struktur jaringan berdasarkan level.
     * * @return array [Level => [UserIDs]]
     */
    private function fetchDownlineTree($maxDepth)
    {
        $tree = []; 
        $allFoundUserIds = [$this->user_id]; // Exclude diri sendiri
        $currentParentUserIds = [$this->user_id]; 

        for ($i = 1; $i <= $maxDepth; $i++) {
            
            $downlineIds = Member::whereIn('parent_user_id', $currentParentUserIds)
                ->whereNotIn('user_id', $allFoundUserIds) // Anti Double Count
                ->whereColumn('user_id', '!=', 'parent_user_id') // Anti Self-Parenting
                ->where('user_id', '!=', $this->user_id) // Safety tambahan
                ->pluck('user_id')
                ->toArray();

            // Jika level ini kosong, berhenti
            if (empty($downlineIds)) {
                break;
            }

            // Simpan hasil level ini
            $tree[$i] = $downlineIds;

            // Update state untuk loop berikutnya
            $allFoundUserIds = array_merge($allFoundUserIds, $downlineIds);
            $currentParentUserIds = $downlineIds;
        }

        return $tree;
    }
}