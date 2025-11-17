<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    use HasFactory;

    protected $table = 'stok_opname';

    protected $fillable = [
        'motor_id',
        'tanggal',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan',
        'petugas',
    ];

    public function motor()
    {
        return $this->belongsTo(Motor::class);
    }

    protected static function booted()
    {
        static::creating(function ($opname) {
            $opname->selisih = $opname->stok_fisik - $opname->stok_sistem;
        });

        static::created(function ($opname) {
            // Setelah opname dibuat, update stok sistem sesuai stok fisik
            $motor = $opname->motor;
            if ($motor) {
                $motor->stok = $opname->stok_fisik;
                $motor->save();
            }
        });
    }
}
