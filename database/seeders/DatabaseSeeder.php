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

        $userIds = [];
        for ($i = 1; $i <= 100; $i++) {
            $userIds[] = DB::table('users')->insertGetId([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role' => $faker->randomElement(['admin', 'member']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $provinces = [
            ['code' => '11', 'name' => 'ACEH'],
            ['code' => '12', 'name' => 'SUMATERA UTARA'],
            ['code' => '13', 'name' => 'SUMATERA BARAT'],
            ['code' => '14', 'name' => 'RIAU'],
            ['code' => '15', 'name' => 'JAMBI'],
            ['code' => '16', 'name' => 'SUMATERA SELATAN'],
            ['code' => '17', 'name' => 'BENGKULU'],
            ['code' => '18', 'name' => 'LAMPUNG'],
            ['code' => '19', 'name' => 'KEPULAUAN BANGKA BELITUNG'],
            ['code' => '21', 'name' => 'KEPULAUAN RIAU'],
            ['code' => '31', 'name' => 'DKI JAKARTA'],
            ['code' => '32', 'name' => 'JAWA BARAT'],
            ['code' => '33', 'name' => 'JAWA TENGAH'],
            ['code' => '34', 'name' => 'DI YOGYAKARTA'],
            ['code' => '35', 'name' => 'JAWA TIMUR'],
            ['code' => '36', 'name' => 'BANTEN'],
            ['code' => '51', 'name' => 'BALI'],
            ['code' => '52', 'name' => 'NUSA TENGGARA BARAT'],
            ['code' => '53', 'name' => 'NUSA TENGGARA TIMUR'],
            ['code' => '61', 'name' => 'KALIMANTAN BARAT'],
            ['code' => '62', 'name' => 'KALIMANTAN TENGAH'],
            ['code' => '63', 'name' => 'KALIMANTAN SELATAN'],
            ['code' => '64', 'name' => 'KALIMANTAN TIMUR'],
            ['code' => '65', 'name' => 'KALIMANTAN UTARA'],
            ['code' => '71', 'name' => 'SULAWESI UTARA'],
            ['code' => '72', 'name' => 'SULAWESI TENGAH'],
            ['code' => '73', 'name' => 'SULAWESI SELATAN'],
            ['code' => '74', 'name' => 'SULAWESI TENGGARA'],
            ['code' => '75', 'name' => 'GORONTALO'],
            ['code' => '76', 'name' => 'SULAWESI BARAT'],
            ['code' => '81', 'name' => 'MALUKU'],
            ['code' => '82', 'name' => 'MALUKU UTARA'],
            ['code' => '91', 'name' => 'PAPUA BARAT'],
            ['code' => '94', 'name' => 'PAPUA'],
        ];

        $provinceIds = [];
        foreach ($provinces as $province) {
            $provinceIds[] = DB::table('provinces')->insertGetId([
                'code' => $province['code'],
                'name' => $province['name'],
            ]);
        }

        $domicileIds = [];
        foreach ($provinceIds as $prov) {
            for ($i = 0; $i < 3; $i++) {
                $domicileIds[] = DB::table('domiciles')->insertGetId([
                    'province_id' => $prov,
                    'code' => $faker->unique()->numerify('###'),
                    'name' => $faker->streetName(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $memberIds = [];
        foreach ($userIds as $uid) {
            $memberIds[] = DB::table('members')->insertGetId([
                'member_code' => strtoupper($faker->bothify('MBR###??')),
                'nik' => $faker->unique()->numerify('################'),
                'user_id' => $uid,
                'parent_member_id' => $faker->optional()->randomElement($memberIds),
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
