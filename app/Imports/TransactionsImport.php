<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Bonus;
use App\Models\Member;
use App\Models\Business;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransactionsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function() use ($rows) {

            foreach ($rows as $row) {
                // Resolve member
                $member = Member::whereHas('user', fn($q) =>
                    $q->whereRaw('LOWER(name) = ?', [strtolower(trim($row['member_name']))])
                )->first();

                if (!$member) {
                    throw new \Exception("Member '{$row['member_name']}' not found"); // rollback
                }

                // Resolve business
                $business = Business::whereRaw('LOWER(name) = ?', [strtolower(trim($row['umkm_name']))])->first();

                if (!$business) {
                    throw new \Exception("Business '{$row['umkm_name']}' not found"); // rollback
                }

                // Insert transaction
                $transaction = Transaction::create([
                    'business_id'      => $business->id,
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

        }); // otomatis rollback kalau ada exception
    }
}
