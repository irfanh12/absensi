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

    public $timestamps = false;

    protected $casts = [
        'status' => 'array',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $hidden = [
        'id',
    ];

    protected function status(): Attribute
    {
        return Attribute::make(
            fn (int $value) => EnumType::getStatusTimesheet($value)
        );
    }

    // protected function createdAt(): Attribute
    // {
    //     return Attribute::make(
    //         fn (int $value) => Carbon::createFromTimestamp($value)->format('D, d M - H:i')
    //     );
    // }
}
