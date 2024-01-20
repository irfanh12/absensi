<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Timesheet extends Model
{
    use HasFactory;

    protected $table = 'timesheet';

    protected $with = ['revision', 'karyawan'];

    public $timestamps = false;

    protected $fillable = [
        'id',
        'karyawan_id',
        'remarks',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => 'array',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    // protected $hidden = [
    //     'id',
    // ];

    protected function status(): Attribute
    {
        return Attribute::make(
            fn (int $value) => EnumType::getStatusTimesheet($value)
        );
    }

    public function karyawan() {
        return $this->hasOne(Karyawan::class, 'id', 'karyawan_id');
    }

    public function revision() {
        return $this->hasOne(TimesheetRevision::class, 'timesheet_id', 'id')->orderByDesc('created_at');
    }

    // protected function createdAt(): Attribute
    // {
    //     return Attribute::make(
    //         fn (int $value) => Carbon::createFromTimestamp($value)->format('D, d M - H:i')
    //     );
    // }
}
