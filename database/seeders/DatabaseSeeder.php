<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Admin Membership',
            'email' => 'admin@membership.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $faker = Faker::create('id_ID');

        // PROVINCE
        $provinceId = DB::table('provinces')->insertGetId([
            'code'       => '51',
            'name'       => 'BALI',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // DOMICILES (Kabupaten/Kota Bali)
        $domiciles = [
            ['code' => '5101', 'name' => 'Badung'],
            ['code' => '5102', 'name' => 'Bangli'],
            ['code' => '5103', 'name' => 'Buleleng'],
            ['code' => '5104', 'name' => 'Gianyar'],
            ['code' => '5105', 'name' => 'Jembrana'],
            ['code' => '5106', 'name' => 'Karangasem'],
            ['code' => '5107', 'name' => 'Klungkung'],
            ['code' => '5108', 'name' => 'Tabanan'],
            ['code' => '5171', 'name' => 'Kota Denpasar'],
        ];

        foreach ($domiciles as $domicile) {
            DB::table('domiciles')->insert([
                'province_id' => $provinceId,
                'code'        => $domicile['code'],
                'name'        => $domicile['name'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        
        $userIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $userIds[] = DB::table('users')->insertGetId([
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'member',
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        }

        for ($i = 11; $i <= 100; $i++) {
            DB::table('users')->insertGetId([
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'member',
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        }

        $memberIds = [];
        foreach ($userIds as $uid) {
            $memberIds[] = DB::table('members')->insertGetId([
            'member_code' => strtoupper($faker->bothify('MBR###??')),
            'nik' => $faker->unique()->numerify('################'),
            'user_id' => $uid,
            'parent_user_id' => $uid,
            'phone_number' => $faker->phoneNumber(),
            'gender' => $faker->randomElement(['male', 'female']),
            'address' => $faker->address(),
            'birth_date' => $faker->date(),
            'npwp' => $faker->numerify('##.###.###.#-###.###'),
            'province_id' => $faker->randomElement($provinceIds),
            'domicile_id' => $faker->randomElement($domicileIds),
            'bank_name' => $faker->randomElement(['BCA', 'BNI', 'BRI', 'Mandiri']),
            'account_number' => $faker->numerify('############'),
            'account_name' => $faker->name(),
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        }

        for ($i = 11; $i <= 100; $i++) {
            $memberIds[] = DB::table('members')->insertGetId([
            'member_code' => strtoupper($faker->bothify('MBR###??')),
            'nik' => $faker->unique()->numerify('################'),
            'user_id' => $i,
            'parent_user_id' => $faker->randomElement($userIds),
            'phone_number' => $faker->phoneNumber(),
            'gender' => $faker->randomElement(['male', 'female']),
            'address' => $faker->address(),
            'birth_date' => $faker->date(),
            'npwp' => $faker->numerify('##.###.###.#-###.###'),
            'province_id' => $faker->randomElement($provinceIds),
            'domicile_id' => $faker->randomElement($domicileIds),
            'bank_name' => $faker->randomElement(['BCA', 'BNI', 'BRI', 'Mandiri']),
            'account_number' => $faker->numerify('############'),
            'account_name' => $faker->name(),
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        }

        $businessIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $businessIds[] = DB::table('businesses')->insertGetId([
                'name' => $faker->company(),
                'address' => $faker->address(),
                'phone' => $faker->phoneNumber(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($userIds as $uid) {
            DB::table('businesses_users')->insert([
                'user_id' => $uid,
                'business_id' => $faker->randomElement($businessIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($memberIds as $mid) {
            DB::table('bonuses')->insert([
                'member_id' => $mid,
                'balance' => rand(0, 500000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 1; $i <= 100; $i++) {
            $amount = rand(10000, 200000);
            $hpp = $amount * 0.7;
            $bonus = $amount * 0.05;

            DB::table('transactions')->insert([
                'business_id' => $faker->randomElement($businessIds),
                'member_id' => $faker->randomElement($memberIds),
                'transaction_code' => strtoupper($faker->bothify('TRX###??')),
                'transaction_date' => $faker->date(),
                'amount' => $amount,
                'hpp' => $hpp,
                'balance' => $amount - $hpp,
                'bonus' => $bonus,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($memberIds as $mid) {
            DB::table('withdrawals')->insert([
                'member_id' => $mid,
                'amount' => rand(0, 500000),
                'date' => $faker->date(),
                'payment_receipt' => $faker->imageUrl(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
