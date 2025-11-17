<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokKeluar extends Model
{
    use HasFactory;

    protected $table = 'stok_keluar';

    protected $fillable = [
        'motor_id',
        'tanggal',
        'jumlah',
        'keterangan',
    ];

    public function motor()
    {
        return $this->belongsTo(Motor::class);
    }

    protected static function booted()
    {
        static::created(function ($stokKeluar) {
            // Kurangi stok motor sesuai jumlah keluar
            $motor = $stokKeluar->motor;
            if ($motor) {
                $motor->stok -= $stokKeluar->jumlah;
                if ($motor->stok < 0) $motor->stok = 0;
                $motor->save();
            }
        });

        static::deleted(function ($stokKeluar) {
            // Jika data stok keluar dihapus, kembalikan stok motor
            $motor = $stokKeluar->motor;
            if ($motor) {
                $motor->stok += $stokKeluar->jumlah;
                $motor->save();
            }
        });
    }
}
