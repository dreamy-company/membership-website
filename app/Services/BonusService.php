<?php

namespace App\Services;

use App\Models\Bonus;
use App\Models\Member;
use App\Models\BonusLog;
use App\Models\Transaction;
use App\Models\BonusSetting;
use Illuminate\Support\Facades\DB;

class BonusService
{
    public function distributeBonus($transactionId)
    {
        // 1. Ambil Data Transaksi
        $transaction = Transaction::find($transactionId);
        
        // 2. Siapa yang belanja? (Si Child/Downline)
        $shopper = Member::find($transaction->member_id); 
        $omzet   = $transaction->amount;

        // 3. Ambil Settingan Persen (Level 1: 10%, Level 2: 5%, dst)
       $schemes = BonusSetting::pluck('percentage', 'level')->toArray();
        // 4. Mulai Mencari Upline (Parent)
        // Start dari Bapaknya si Shopper
        $currentUplineId = $shopper->parent_user_id; 
        $currentLevel    = 1; // Jarak generasi (1 = Bapak kandung, 2 = Kakek)

        // Loop ke atas (Memanjat pohon upline)
        while ($currentUplineId && isset($schemes[$currentLevel])) {
            
            // Ambil data Upline
            $upline = Member::where('user_id', $currentUplineId)->first();

            // Cek Rule 1.4 & 1.5 (Upline harus Aktif)
            if (!$upline || $upline->status !== 'active') {
                // Jika Upline mati/non-aktif, bonus LEVEL INI hangus/skip
                if ($upline) {
                    $currentUplineId = $upline->parent_user_id; // Lanjut ke atasnya lagi
                    $currentLevel++; // Level tetap nambah (agar kakek tetap dapat jatah Level 2, bukan Level 1)
                } else {
                    break; // Upline sudah habis (pucuk)
                }
                continue;
            }

            // Hitung Bonus
            $percentage  = $schemes[$currentLevel]; 
            $bonusAmount = $omzet * ($percentage / 100);

            // CATAT BONUS (Uang masuk ke Upline)
            BonusLog::create([
                'member_id'      => $upline->id,   // <--- PENERIMA (Parent)
                'from_member_id' => $shopper->id,  // <--- SUMBER (Child yang belanja)
                'transaction_id' => $transaction->id,
                'level'          => $currentLevel, // "Bonus Generasi ke-X"
                'percentage'     => $percentage,
                'amount'         => $bonusAmount,
            ]);

            // Persiapan Loop Berikutnya (Naik ke Bapaknya Upline / Kakek)
            $currentUplineId = $upline->parent_user_id;
            
            // Safety break (cegah infinite loop jika database error parent=diri sendiri)
            if ($currentUplineId == $upline->user_id) break;

            $currentLevel++;
        }
    }
}