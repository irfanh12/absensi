<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach([ 'Administrator', 'Klien', 'Manager', 'Human Resource', 'Supervisor', 'Karyawan', 'Karyawan Outsource' ] as $id => $type) {
            DB::table('user_types')
                ->insert([
                    'id' => $id+1,
                    'type' => $type
                ]);
        }
    }
}
