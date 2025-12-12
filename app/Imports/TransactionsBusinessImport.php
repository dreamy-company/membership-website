<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Bonus;
use App\Models\Member;
use App\Models\Business;
use App\Models\Transaction;
use App\Models\BusinessesUsers;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransactionsBusinessImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // Business ID tetap
        $businessId = BusinessesUsers::where('user_id', auth()->id())->value('business_id'); // ganti sesuai business yang dituju

        foreach ($rows as $row) {
            // Cari member berdasarkan nama
            $member = Member::whereHas('user', fn($q) => 
                $q->whereRaw('LOWER(name) = ?', [strtolower(trim($row['member_name']))])
            )->first();

            if (!$member) {
                continue; // skip kalau member ga ketemu
            }

            // Insert transaction
            $transaction = Transaction::create([
                'business_id'      => $businessId, // fixed
                'member_id'        => $member->id,
                'transaction_code' => $row['transaction_code'] ?? '-',
                'transaction_date' => now()->toDateString(),
                'amount'           => $row['amount'] ?? 0,
                'hpp'              => $row['hpp'] ?? 0,
                'balance'          => $row['balance'] ?? 0,
                'bonus'            => $row['bonus'] ?? 0,
            ]);

            // Update bonus
            $bonus = Bonus::firstOrNew(['member_id' => $member->id]);
            $bonus->balance = ($bonus->balance ?? 0) + $transaction->bonus;
            $bonus->save();
        }
    }

}
