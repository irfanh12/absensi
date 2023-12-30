<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnumType extends Model
{
    use HasFactory;

    public const ADMINISTRATOR = 'admin';
    public const HUMAN_RESOURCE = 'hr';
    public const KLIEN = 'klien';
    public const MANAGER = 'manager';
    public const SUPERVISOR = 'supervisor';
    public const KARYAWAN = 'karyawan';
    public const KARYAWAN_OUTSOURCE = 'outsource';

    // Approval
    public const APPROVAL_KLIEN     = 1;
    public const APPROVAL_HR        = 2;

    public static function getConstant(string $constant)
    {
        return constant("self::$constant") ?? null;
    }
}
