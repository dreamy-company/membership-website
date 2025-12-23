<?php

namespace App\Imports;

use App\Models\Bonus;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\BusinessesUsers;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransactionsBusinessImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // Wrap all operations in a transaction
        DB::transaction(function() use ($rows) {

            // Business ID tetap
            $businessId = BusinessesUsers::where('user_id', auth()->id())->value('business_id');

            if (!$businessId) {
                throw new \Exception("Business ID not found for current user");
            }

            foreach ($rows as $row) {
                // Cari member berdasarkan nama
                $member = Member::whereHas('user', fn($q) => 
                    $q->whereRaw('LOWER(name) = ?', [strtolower(trim($row['member_name']))])
                )->first();

                if (!$member) {
                    throw new \Exception("Member '{$row['member_name']}' not found"); // throw agar rollback
                }

                // Insert transaction
                $transaction = Transaction::create([
                    'business_id'      => $businessId,
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

        }); // DB::transaction otomatis rollback kalau ada exception
    }
}
