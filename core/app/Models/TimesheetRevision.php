<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimesheetRevision extends Model
{
    use HasFactory;

    protected $table = 'timesheet_revision';

    protected $with = ['karyawan'];

    public $timestamps = false;

    protected $casts = [
        'status' => 'array',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    public function karyawan() {
        return $this->hasOne(Karyawan::class, 'id', 'karyawan_id');
    }
}
