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

    /**
     * Retrieves the value of a class constant.
     *
     * @param string $constant The name of the constant.
     * @return mixed The value of the constant, or null if it does not exist.
     */
    public static function getConstant(string $constant)
    {
        return constant("self::$constant") ?? null;
    }

    /**
     * Retrieves the status of a timesheet based on the given status code.
     *
     * @param int $status The status code of the timesheet.
     * @return string The corresponding status of the timesheet.
     */
    public static function getStatusTimesheet(int $status)
    {
        switch ($status) {
            case 1:
                return [
                    'label' => 'Approved Client',
                    'class' => 'bg-info',
                ];
            case 2:
                return [
                    'label' => 'Approved HR',
                    'class' => 'bg-success',
                ];
            default:
                return [
                    'label' => 'Pending',
                    'class' => 'bg-black-50',
                ];
        }
    }
}
