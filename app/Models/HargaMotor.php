<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaMotor extends Model
{
    use HasFactory;

    protected $table = 'harga_motor';

    protected $fillable = [
        'motor_id',
        'harga_cash',
        'uang_muka',
        'angsuran',
        'lama_kredit',
    ];

    public function motor()
    {
        return $this->belongsTo(Motor::class);
    }
}
