<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $with = ['perusahaan', 'user_type'];

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Get the user's last name.
     */
    protected function firstName(): Attribute
    {
        return Attribute::make(
            fn (string $value) => ucfirst(strtolower($value))
        );
    }

    /**
     * Get the user's last name.
     */
    protected function lastName(): Attribute
    {
        return Attribute::make(
            fn (string $value) => ucfirst(strtolower($value))
        );
    }

    /**
     * Get the user's last name.
     */
    protected function fullname(): Attribute
    {
        $firstName = $this->first_name;
        $lastName = $this->last_name;
        return Attribute::make(
            fn () => "$firstName $lastName",
        );
    }

    /**
     * Get the user's last name.
     */
    protected function initialName(): Attribute
    {
        $firstName = $this->first_name;
        $lastName = $this->last_name;
        return Attribute::make(
            fn () => strtoupper(substr($firstName, 0, 1).''.substr($lastName, 0, 1)),
        );
    }

    public function user_type() {
        return $this->hasOne(UserType::class, 'id', 'type_id');
    }

    public function perusahaan() {
        return $this->hasOne(Perusahaan::class, 'id', 'perusahaan_id');
    }
}
