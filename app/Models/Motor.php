<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motor extends Model
{
    use HasFactory;

    // Nama tabel (karena kita pakai singular)
    protected $table = 'motor';

    // Kolom yang bisa diisi
    protected $fillable = [
        'kode_motor',
        'nama_motor',
        'tipe',
        'warna',
        'tahun',
        'harga_beli',
        'harga_jual',
        'stok',
        'status',
        'foto',
    ];

    // Format harga otomatis (opsional tapi keren buat tampil di view)
    public function getHargaBeliFormatAttribute()
    {
        return 'Rp ' . number_format($this->harga_beli, 0, ',', '.');
    }

    public function getHargaJualFormatAttribute()
    {
        return 'Rp ' . number_format($this->harga_jual, 0, ',', '.');
    }

    // Scope pencarian cepat (buat nanti di Filament Table)
    public function scopeSearch($query, $keyword)
    {
        return $query->where('nama_motor', 'like', "%$keyword%")
                     ->orWhere('kode_motor', 'like', "%$keyword%")
                     ->orWhere('tipe', 'like', "%$keyword%");
    }
}
