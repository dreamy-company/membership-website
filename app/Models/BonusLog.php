<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusLog extends Model
{
    // Izinkan semua kolom diisi (mass assignment)
    protected $guarded = ['id'];

    // Relasi ke Penerima (Saya/Upline)
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    // Relasi ke Pengirim/Sumber (Downline)
    public function sourceMember()
    {
        return $this->belongsTo(Member::class, 'from_member_id');
    }

    // Relasi ke Transaksi Asli
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}