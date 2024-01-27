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

    public static function getStatusTime($times, $jamkerja, $report = false) {
        if (!$times) {
            if($report) {
                return [ 'Absensi Masih Kosong' ];
            } else {
                return [
                    [
                        'label' => 'Absensi Masih Kosong',
                        'class' => 'bg-danger',
                    ]
                ];
            }
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
                if($report) {
                    $result[] = 'Libur';
                } else {
                    $result[] = [
                        'label' => 'Libur',
                        'class' => 'bg-danger',
                    ];
                }
            } else {
                if($report) {
                    $result[] = 'Lembur Waktu Libur';
                } else {
                    $result[] = [
                        'label' => 'Lembur Waktu Libur',
                        'class' => 'bg-danger',
                    ];
                }
            }
        }

        if ($userTimeIn === 0 && $userTimeOut === 0) {
            if($report) {
                $result[] = 'Tidak Hadir';
            } else {
                $result[] = [
                    'label' => 'Tidak Hadir',
                    'class' => 'bg-danger',
                ];
            }
        }
        else {
            if($report) {
                $result[] = 'Hadir';
            } else {
                $result[] = [
                    'label' => 'Hadir',
                    'class' => 'bg-success',
                ];
            }
        }

        if ($userTimeIn && $userTimeIn - $inWorkTime > $lateThreshold && $inWorkTime) {
            if($report) {
                $result[] = 'Terlambat';
            } else {
                $result[] = [
                    'label' => 'Terlambat',
                    'class' => 'bg-danger',
                ];
            }
        }

        if ($userTimeIn && $userTimeIn - $inWorkTime < $earlyThreshold) {
            if($report) {
                $result[] = 'Masuk Lebih Cepat';
            } else {
                $result[] = [
                    'label' => 'Masuk Lebih Cepat',
                    'class' => 'bg-info',
                ];
            }
        }

        if ($userTimeOut && $userTimeOut - $endWorkTime < $earlyThreshold) {
            if($report) {
                $result[] = 'Pulang Lebih Cepat';
            } else {
                $result[] = [
                    'label' => 'Pulang Lebih Cepat',
                    'class' => 'bg-info',
                ];
            }
        }

        if ($userTimeOut && $userTimeOut < $userTimeIn) {
            if($report) {
                $result[] = 'Tidak Mengisi Absen Pulang';
            } else {
                $result[] = [
                    'label' => 'Tidak Mengisi Absen Pulang',
                    'class' => 'bg-warning',
                ];
            }
        }

        if ($userTimeIn && $userTimeIn < $userTimeOut) {
            $workedDuration = $userTimeOut - $userTimeIn;
            if ($workedDuration > $shiftDuration) {
                if($report) {
                    $result[] = 'Lembur';
                } else {
                    $result[] = [
                        'label' => 'Lembur',
                        'class' => 'bg-warning',
                    ];
                }
            } else {
                if($report) {
                    $result[] = 'Tidak Mengisi Absen Masuk';
                } else {
                    $result[] = [
                        'label' => 'Tidak Mengisi Absen Masuk',
                        'class' => 'bg-warning',
                    ];
                }
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
