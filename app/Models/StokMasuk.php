<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokMasuk extends Model
{
    use HasFactory;

    protected $table = 'stok_masuk';

    protected $fillable = [
        'motor_id',
        'tanggal',
        'jumlah',
        'keterangan',
    ];

    /**
     * Relasi ke tabel Motor
     * Setiap stok masuk pasti terkait ke satu motor
     */
    public function motor()
    {
        return $this->belongsTo(Motor::class);
    }

    protected static function booted()
    {
        static::created(function ($stokMasuk) {
            // Tambah stok motor sesuai jumlah masuk
            $motor = $stokMasuk->motor;
            if ($motor) {
                $motor->stok += $stokMasuk->jumlah;
                $motor->save();
            }
        });

        static::deleted(function ($stokMasuk) {
            // Jika data stok masuk dihapus, kurangi stok motor
            $motor = $stokMasuk->motor;
            if ($motor) {
                $motor->stok -= $stokMasuk->jumlah;
                if ($motor->stok < 0) $motor->stok = 0;
                $motor->save();
            }
        });
    }
}
