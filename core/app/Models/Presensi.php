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

    public function karyawan() {
        return $this->hasOne(Karyawan::class, 'id', 'karyawan_id');
    }

    public function jamkerja() {
        return $this->hasOne(JamKerja::class, 'id', 'jamkerja_id');
    }
}
