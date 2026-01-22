<?php

namespace App\Services;

use App\Models\Member;
use App\Models\BonusLog;
use App\Models\Bonus;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class BonusService
{
    // Konfigurasi Persentase Bonus per Level
    // Level 1 = Upline Langsung, Level 2 = Kakeknya, dst.
    protected $schemes = [
        1 => 0.10, // 10% dari Omzet
        2 => 0.05, // 5%
        3 => 0.02, // 2%
        4 => 0.01, // 1%
        5 => 0.01, // 1%
    ];

    public function distributeBonus($transactionId)
    {
        $transaction = Transaction::find($transactionId);
        if (!$transaction) return;

        $sourceMember = Member::find($transaction->member_id);
        if (!$sourceMember) return;

        // Omzet dasar perhitungan (Amount transaksi)
        $omzet = $transaction->amount; 

        // Mulai mencari upline dari parent
        $currentParentUserId = $sourceMember->parent_user_id;
        $currentLevel = 1;

        // Loop memanjat ke atas
        while ($currentParentUserId && isset($this->schemes[$currentLevel])) {
            
            $upline = Member::where('user_id', $currentParentUserId)->first();
            if (!$upline) break;

            $percentage = $this->schemes[$currentLevel];
            $bonusAmount = $omzet * $percentage;

            // Masukkan ke DB
            // Kita tidak perlu DB::transaction lagi di sini karena 
            // TransactionsImport sudah membungkus semuanya dalam DB::transaction
            
            // A. Catat Log
            // BonusLog::create([
            //     'member_id'      => $upline->id,
            //     'from_member_id' => $sourceMember->id,
            //     'transaction_id' => $transaction->id,
            //     'level'          => $currentLevel,
            //     'percentage'     => $percentage * 100,
            //     'amount'         => $bonusAmount,
            // ]);

            // B. Update Saldo Upline
            $wallet = Bonus::firstOrCreate(
                ['member_id' => $upline->id],
                ['balance' => 0]
            );
            $wallet->increment('balance', $bonusAmount);

            // C. Naik ke level berikutnya
            $currentParentUserId = $upline->parent_user_id;
            
            // Prevent infinite loop (jika parent adalah diri sendiri)
            if ($currentParentUserId == $upline->user_id) break;

            $currentLevel++;
        }
    }
}