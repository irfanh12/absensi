<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Helper extends Model
{
    use HasFactory;

    public static function unixTimeToDate($time, $format = 'Y-m-d') {
        return Carbon::createFromTimestamp($time)->format($format);
    }
}
