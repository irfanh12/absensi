<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamKerja extends Model
{
    use HasFactory;

    protected $table = 'jamkerja';

    protected $with = ['shift'];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
