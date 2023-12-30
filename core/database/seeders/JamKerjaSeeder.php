<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JamKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleData = [
            [
                'nama_shift'    => 'Office Hour',
                'created_at'        => now()->timestamp,
                'jamkerja'      => [
                    [
                        'shift_id'      => '',
                        'hari'          => 'Senin',
                        'start_time'    => '09:00',
                        'end_time'      => '18:00',
                        'created_at'    => now()->timestamp,
                    ],
                    [
                        'shift_id'      => '',
                        'hari'          => 'Selasa',
                        'start_time'    => '09:00',
                        'end_time'      => '18:00',
                        'created_at'    => now()->timestamp,
                    ],
                    [
                        'shift_id'      => '',
                        'hari'          => 'Rabu',
                        'start_time'    => '09:00',
                        'end_time'      => '18:00',
                        'created_at'    => now()->timestamp,
                    ],
                    [
                        'shift_id'      => '',
                        'hari'          => 'Kamis',
                        'start_time'    => '09:00',
                        'end_time'      => '18:00',
                        'created_at'    => now()->timestamp,
                    ],
                    [
                        'shift_id'      => '',
                        'hari'          => 'Jumat',
                        'start_time'    => '09:00',
                        'end_time'      => '18:00',
                        'created_at'    => now()->timestamp,
                    ],
                    [
                        'shift_id'      => '',
                        'hari'          => 'Sabtu',
                        'start_time'    => null,
                        'end_time'      => null,
                        'created_at'    => now()->timestamp,
                    ],
                    [
                        'shift_id'      => '',
                        'hari'          => 'Minggu',
                        'start_time'    => null,
                        'end_time'      => null,
                        'created_at'    => now()->timestamp,
                    ],
                ]
            ]
        ];

        foreach($sampleData as $data) {
            $shiftId = DB::table('shift')->insertGetId([
                'nama_shift' => $data['nama_shift'],
                'created_at' => $data['created_at'],
            ]);

            foreach($data['jamkerja'] as $jamkerja) {
                $jamkerja['shift_id'] = $shiftId;
                DB::table('jamkerja')->insert($jamkerja);
            }
        }
    }
}
