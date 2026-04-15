<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $guarded = ['ID'];

    public function scopeSearch($query, $term)
    {
        if ($term) {
            $term = "%{$term}%";

            $query->where(function ($q) use ($term) {
                $q->where('CustCode', 'like', $term)
                    ->orWhere('CustDesc', 'like', $term)
                    ->orWhere('SalesNumber', 'like', $term)
                    ->orWhere('SalesDate', 'like', $term)
                    ->orWhere('SalesTime', 'like', $term)
                    ->orWhere('TransactionCode', 'like', $term)
                    ->orWhereHas('business', function ($businessQuery) use ($term) {
                        $businessQuery->where('name', 'like', $term);
                    })
                    ->orWhereHas('transaction.member.user', function ($memberQuery) use ($term) {
                        $memberQuery->where('name', 'like', $term);
                    });
            });
        }
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'IDBisnis');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'IDTransaction');
    }
}