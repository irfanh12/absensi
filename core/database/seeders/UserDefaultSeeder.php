<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class UserDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create('id_ID');
        $birthday = $faker->dateTimeBetween('-50 years', 'now');
        $sampleData = [
            [
                'perusahaan_id' => 0,
                'identify_id' => '-',
                'type_id' => 1,
                'position' => 'Administrator',
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'phone_number' => $faker->phoneNumber(),
                'birthdate' => $birthday->format('Y-m-d'),
                'gender' => 'male',
                'address' => $faker->address(),
                'salary' => 10000000.00,
                'created_at' => now()->timestamp,
                'updated_at' => now()->timestamp,
                'user' => [
                    'email' => 'administrator@example.com',
                    'password' => Hash::make('admin123'),
                    'created_at' => now()->timestamp,
                    'updated_at' => now()->timestamp,
                ]
            ],
            [
                'perusahaan_id' => 'e2d43ef5-cc0c-4ebe-bdb0-f66dbb230d51',
                'identify_id' => $faker->nik(),
                'type_id' => 3,
                'position' => 'Human Resource',
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'phone_number' => $faker->phoneNumber(),
                'birthdate' => $birthday->format('Y-m-d'),
                'gender' => 'male',
                'address' => $faker->address(),
                'salary' => 10000000.00,
                'created_at' => now()->timestamp,
                'updated_at' => now()->timestamp,
                'user' => [
                    'email' => 'hr@example.com',
                    'password' => Hash::make('hr123'),
                    'created_at' => now()->timestamp,
                    'updated_at' => now()->timestamp,
                ]
            ],
        ];

        foreach($sampleData as $data) {
            $uuid = (string) Str::uuid();
            $data['id'] = $uuid;

            $user = $data['user'];
            $user['id'] = $uuid;

            unset($data['user']);
            DB::table('karyawan')->insert($data);
            DB::table('users')->insert($user);
        }
    }
}
