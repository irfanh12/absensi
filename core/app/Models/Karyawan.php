<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $with = ['user_type'];

    public $incrementing = false;
    protected $keyType = 'string';

    public function user_type() {
        return $this->hasOne(UserType::class, 'id', 'type_id');
    }
}
