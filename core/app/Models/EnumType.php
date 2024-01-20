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
     * Retrieves the status, label, and class of a timesheet based on the given status code.
     *
     * @param int $status The status code of the timesheet.
     * @return array Returns an array containing the label and class of the timesheet status.
     */
    public static function getStatusTimesheet(int $status)
    {
        $result = [];
        switch ($status) {
            case 1:
                $result = [
                    'label' => 'Approved Client',
                    'class' => 'bg-info',
                ];
                break;
            case 2:
                $result = [
                    'label' => 'Approved HR',
                    'class' => 'bg-success',
                ];
                break;
            case 3:
                $result = [
                    'label' => 'Rejected Client',
                    'class' => 'bg-danger',
                ];
                break;
            case 4:
                $result = [
                    'label' => 'Rejected HR',
                    'class' => 'bg-danger',
                ];
                break;
            default:
                $result = [
                    'label' => 'Pending',
                    'class' => 'bg-black-50',
                ];
                break;
        }

        return $result;
    }
}
