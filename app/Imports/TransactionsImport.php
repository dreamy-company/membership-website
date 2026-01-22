<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Bonus;
use App\Models\Member;
use App\Models\Business;
use App\Models\Transaction;
use App\Services\BonusService; // [PENTING] Load Service Bonus
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class TransactionsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // Cek header apa yang dibaca sistem
        // if ($rows->isEmpty()) {
        //     dd('Data Kosong! Cek file Excelnya.');
        // }
        
        // // Lihat baris pertama, kuncinya (key) apa aja?
        // dd($rows->first());

        DB::transaction(function() use ($rows) {

            // Inisialisasi Service Bonus Sekali Saja
            $bonusService = new BonusService();

            foreach ($rows as $row) {
                // 1. Resolve Member (Cari berdasarkan Nama)
                // Catatan: Lebih aman pakai 'member_code' jika ada di Excel, tapi pakai nama juga oke.
                $member = Member::whereHas('user', fn($q) =>
                    $q->whereRaw('LOWER(name) = ?', [strtolower(trim($row['member_name']))])
                )->first();

                if (!$member) {
                    // Opsi: throw error (rollback semua) atau continue (skip baris ini)
                    throw new \Exception("Member '{$row['member_name']}' tidak ditemukan."); 
                }

                // 2. Resolve Business (UMKM)
                $business = Business::whereRaw('LOWER(name) = ?', [strtolower(trim($row['umkm_name']))])->first();

                if (!$business) {
                    throw new \Exception("Business '{$row['umkm_name']}' tidak ditemukan.");
                }

                // 3. Parsing Tanggal (Jaga-jaga format Excel aneh)
                try {
                    // Cek kolom 'trx_date' atau 'transaction_date' dari Excel
                    $excelDate = $row['transaction_date'] ?? $row['trx_date'] ?? now();
                    $trxDate = Carbon::parse($excelDate)->toDateString();
                } catch (\Exception $e) {
                    $trxDate = now()->toDateString();
                }

                // 4. Insert Transaction
                $transaction = Transaction::create([
                    'business_id'      => $business->id,
                    'member_id'        => $member->id,
                    'transaction_code' => $row['transaction_code'] ?? 'TRX-' . time() . rand(100,999),
                    'transaction_date' => $trxDate,
                    'amount'           => $row['amount'] ?? 0,
                    'hpp'              => $row['hpp'] ?? 0,
                    'balance'          => $row['balance'] ?? 0,
                    'bonus'            => $row['bonus'] ?? 0, // Ini bonus langsung/cashback (jika ada)
                ]);

                // 5. Update Bonus Langsung (Cashback ke Pembeli)
                // Jika kolom 'bonus' di Excel maksudnya adalah cashback pribadi
                if ($transaction->bonus > 0) {
                    $bonusWallet = Bonus::firstOrNew(['member_id' => $member->id]);
                    $bonusWallet->balance = ($bonusWallet->balance ?? 0) + $transaction->bonus;
                    $bonusWallet->save();
                }

                // 6. [LOGIKA BARU] Distribusi Bonus ke Upline (Unilevel)
                // Kita panggil service untuk memanjat pohon dan bagi-bagi komisi
                $bonusService->distributeBonus($transaction->id);
            }

        }); // End DB Transaction
    }
}