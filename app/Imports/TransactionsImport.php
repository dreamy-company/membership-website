<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Bonus;
use App\Models\Member;
use App\Models\Business;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransactionsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Misal pakai nama
            $memberId = Member::where('user_id', User::where('name', $row['member_name'])->value('id'))->value('id');

            // Resolve business by name
            $business = Business::whereRaw('LOWER(name) = ?', [strtolower(trim($row['umkm_name']))])->first();

            // Resolve member by user name
            $member = Member::whereHas('user', fn($q) => 
                $q->whereRaw('LOWER(name) = ?', [strtolower(trim($row['member_name']))])
            )->first();

            if (!$business || !$member) {
                // skip row kalau business/member ga ketemu
                continue;
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

            $bonus = Bonus::firstOrNew(['member_id' => $memberId]);
            $bonus->balance = ($bonus->balance ?? 0) + $transaction->bonus;
            $bonus->save();

        }
    }
}
