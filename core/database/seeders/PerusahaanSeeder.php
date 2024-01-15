<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class PerusahaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create('id_ID');
        $sampleData = [
            [
                'id'                => 0,
                'identify_id'       => '-',
                'nama_perusahaan'   => 'Administrator',
                'address'           => '-',
                'phone_number'      => 0,
                'created_at'        => now()->timestamp,
            ],
            [
                'id'                => 'e2d43ef5-cc0c-4ebe-bdb0-f66dbb230d51',
                'identify_id'       => '3277021232141234',
                'nama_perusahaan'   => $faker->company(),
                'address'           => '-',
                'phone_number'      => 0,
                'created_at'        => now()->timestamp,
            ],
        ];

        foreach($sampleData as $data)
            DB::table('perusahaan')->insert($data);
    }
}
