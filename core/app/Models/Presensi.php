<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $with = ['karyawan', 'jamkerja'];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    public static function getStatusTime($times, $jamkerja) {
        if (!$times) {
            return [
                [
                    'label' => 'Absensi Masih Kosong',
                    'class' => 'bg-danger',
                ]
            ];
        }

        $lateThreshold = 5 * 60; // 5 minutes in seconds
        $earlyThreshold = -5 * 60; // 5 minutes in seconds
        $shiftDuration = 8 * 60 * 60; // 8 hours in seconds

        $userTimeIn = strtotime($times[0] ?? null);
        $userTimeOut = strtotime($times[1] ?? null);
        $inWorkTime = strtotime($jamkerja->start_time);
        $endWorkTime = strtotime($jamkerja->end_time);

        $result = [];

        if (!$inWorkTime && !$endWorkTime) {
            if ($userTimeIn === 0 && $userTimeOut === 0) {
                $result[] = [
                    'label' => 'Libur',
                    'class' => 'bg-danger',
                ];
            } else {
                $result[] = [
                    'label' => 'Lembur Waktu Libur',
                    'class' => 'bg-danger',
                ];
            }
        }

        if ($userTimeIn === 0 && $userTimeOut === 0) {
            $result[] = [
                'label' => 'Tidak Hadir',
                'class' => 'bg-danger',
            ];
        } else {
            $result[] = [
                'label' => 'Hadir',
                'class' => 'bg-success',
            ];
        }

        if ($userTimeIn && $userTimeIn - $inWorkTime > $lateThreshold && $inWorkTime) {
            $result[] = [
                'label' => 'Terlambat',
                'class' => 'bg-danger',
            ];
        }

        if ($userTimeIn && $userTimeIn - $inWorkTime < $earlyThreshold) {
            $result[] = [
                'label' => 'Masuk Lebih Cepat',
                'class' => 'bg-info',
            ];
        }

        if ($userTimeOut && $userTimeOut - $endWorkTime < $earlyThreshold) {
            $result[] = [
                'label' => 'Pulang Lebih Cepat',
                'class' => 'bg-info',
            ];
        }

        if ($userTimeOut && $userTimeOut < $userTimeIn) {
            $result[] = [
                'label' => 'Tidak Mengisi Absen Pulang',
                'class' => 'bg-warning',
            ];
        }

        if ($userTimeIn && $userTimeIn < $userTimeOut) {
            $workedDuration = $userTimeOut - $userTimeIn;
            if ($workedDuration > $shiftDuration) {
                $result[] = [
                    'label' => 'Lembur',
                    'class' => 'bg-warning',
                ];
            } else {
                $result[] = [
                    'label' => 'Tidak Mengisi Absen Masuk',
                    'class' => 'bg-warning',
                ];
            }
        }

        return $result;
    }

    public static function getStatusEndTime($time, $end_time, $status) {
        if($time) {
            $time = strtotime($time);
            $end_time = strtotime($end_time);
            if ($userTimeOut - $start_time < $earlyThreshold) {
                echo "Early Out";
            }
            if ($userTimeOut < $time) {
                echo "No Swipe Out";
            }
            if ($time < $userTimeOut) {
                echo "No Swipe In";
            }
            dd(strtotime($time), strtotime($end_time));
        }
        return $status;
    }

    public function karyawan() {
        return $this->hasOne(Karyawan::class, 'id', 'karyawan_id');
    }

    public function jamkerja() {
        return $this->hasOne(JamKerja::class, 'id', 'jamkerja_id');
    }
}
