<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Transaction;
use App\Models\BonusLevelSetup;

class BonusService
{
    // Terima parameter ke-2: $baseBonusAmount (Nilai asli 100.000)
    public function distributeBonus($originalTransactionId, $baseBonusAmount)
    {
        $originalTrx = Transaction::find($originalTransactionId);
        
        // Identifikasi Shopper
        $shopper = Member::find($originalTrx->member_id); 

        // Ambil Settingan Bonus
        $bonusSetups = BonusLevelSetup::pluck('persenBonus', 'kodeBonus')->toArray();

        // ============================================================
        // TAHAP A: BONUS LEVEL 1 (Untuk Diri Sendiri/Shopper)
        // Note: Leader sudah dihandle di Row Asli, jadi skip.
        // ============================================================
        
        if (isset($bonusSetups['Level 1'])) {
            $this->createBonusTransaction(
                $originalTrx, 
                $shopper, 
                'Level 1', 
                $bonusSetups['Level 1'], 
                $baseBonusAmount
            );
        }

        // ============================================================
        // TAHAP B: BONUS UNTUK UPLINE (LEVEL 2 KE ATAS)
        // ============================================================
        
        $currentUplineId = $shopper->parent_user_id; 
        $currentLevel    = 2; // Start Level 2

        while ($currentUplineId) {
            
            $levelKey = "Level " . $currentLevel;

            if (!isset($bonusSetups[$levelKey])) break; 
            if ($currentUplineId == $shopper->user_id) break;

            $upline = Member::where('user_id', $currentUplineId)->first();

            if ($upline && $upline->status === 'active') {
                
                $this->createBonusTransaction(
                    $originalTrx, 
                    $upline, 
                    $levelKey, 
                    $bonusSetups[$levelKey], 
                    $baseBonusAmount
                );

                if ($upline->parent_user_id == $upline->user_id) break;
                
                $currentUplineId = $upline->parent_user_id;
                $currentLevel++;

            } else {
                // Pass-up logic
                if ($upline) {
                    $currentUplineId = $upline->parent_user_id;
                    $currentLevel++; 
                } else {
                    break;
                }
            }
        }
    }

    private function createBonusTransaction($originalTrx, $receiverMember, $levelName, $percent, $baseBonus)
    {
        $calculatedBonus = $baseBonus * ($percent / 100);

        Transaction::create([
            'business_id'      => $originalTrx->business_id,
            'member_id'        => $receiverMember->id,
            'user_id'          => $receiverMember->user_id,
            'transaction_id'   => $originalTrx->user_id, // Sumber (Shopper)
            
            // UPDATE: Transaction Code SAMA PERSIS dengan Invoice Asli
            'transaction_code' => $originalTrx->transaction_code, 
            
            'transaction_date' => now(),
            'transaction_time' => now(),
            
            // Copy Data Sumber
            'amount'           => $originalTrx->amount,
            'hpp'              => $originalTrx->hpp,
            'balance'          => $originalTrx->balance, 

            // Info Bonus
            'LevelMember'      => $levelName, 
            'BonusPercent'     => $percent,
            
            // Hasil Kalkulasi (Persen * Base)
            'bonus'            => $calculatedBonus, 
        ]);
    }
}