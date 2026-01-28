<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\Business;
use App\Models\Transaction;
use App\Models\BonusLog;     // [BARU] Untuk catat cashback pribadi
use App\Services\BonusService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Exception;

class TransactionsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function() use ($rows) {

            // Inisialisasi Service Bonus
            $bonusService = new BonusService();


            foreach ($rows as $row) {
                // ---------------------------------------------------------
                // 1. VALIDASI & PENCARIAN DATA
                // ---------------------------------------------------------
                
                // Cari Member (Case Insensitive)
                $memberName = trim($row['member_name']);
                $member = Member::whereHas('user', fn($q) =>
                    $q->whereRaw('LOWER(name) = ?', [strtolower($memberName)])
                )->first();

                if (!$member) {
                    throw new Exception("Member dengan nama '{$memberName}' tidak ditemukan."); 
                }

                // Cari UMKM (Case Insensitive)
                $umkmName = trim($row['umkm_name']);
                $business = Business::whereRaw('LOWER(name) = ?', [strtolower($umkmName)])->first();

                if (!$business) {
                    throw new Exception("Business/UMKM '{$umkmName}' tidak ditemukan.");
                }

                // Parsing Tanggal (Handle format Excel yang kadang angka serial)
                try {
                    $excelDate = $row['transaction_date'] ?? $row['trx_date'] ?? now();
                    // Jika format excel angka serial (misal: 44562), Carbon butuh handling khusus
                    if (is_numeric($excelDate)) {
                        $trxDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($excelDate)->format('Y-m-d');
                    } else {
                        $trxDate = Carbon::parse($excelDate)->toDateString();
                    }
                } catch (Exception $e) {
                    $trxDate = now()->toDateString();
                }

                // ---------------------------------------------------------
                // 2. SIMPAN TRANSAKSI UTAMA
                // ---------------------------------------------------------
                $transaction = Transaction::create([
                    'business_id'      => $business->id,
                    'member_id'        => $member->id,
                    'transaction_code' => $row['transaction_code'] ?? 'TRX-' . strtoupper(uniqid()),
                    'transaction_date' => $trxDate,
                    'amount'           => $row['amount'] ?? 0,
                    'hpp'              => $row['hpp'] ?? 0,
                    'balance'          => 0, // Kolom ini sebenarnya sdh tidak relevan, set 0 saja
                    'bonus'            => $row['bonus'] ?? 0, // Ini nominal cashback pribadi dr Excel
                ]);

                // ---------------------------------------------------------
                // 3. HANDLE CASHBACK PRIBADI (Jika ada di Excel)
                // ---------------------------------------------------------
                // Jika di Excel kolom 'bonus' ada isinya, kita catat sebagai BonusLog Level 0
                // Tujuannya: Agar saldo member bertambah sesuai Rule 6.4
                $directBonus = floatval($row['bonus'] ?? 0);
                
                if ($directBonus > 0) {
                    BonusLog::create([
                        'member_id'      => $member->id,      // Penerima: Diri Sendiri
                        'from_member_id' => $member->id,      // Sumber: Diri Sendiri
                        'transaction_id' => $transaction->id,
                        'level'          => 0,                // Level 0 = Cashback Pribadi
                        'percentage'     => 0,                // 0% (karena nominal fix dari Excel)
                        'amount'         => $directBonus,
                    ]);
                }

                // ---------------------------------------------------------
                // 4. DISTRIBUSI BONUS UPLINE (Level 1, 2, dst)
                // ---------------------------------------------------------
                // Panggil service untuk memanjat ke atas (Parent)
                $bonusService->distributeBonus($transaction->id);
            }

        });
    }
}