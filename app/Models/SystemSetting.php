<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_setting';

    protected $fillable = [
        'nama_sistem',
        'nama_perusahaan',
        'telepon',
        'alamat',
        'logo',
        'login_logo',
        'favicon',
        'footer_text',
    ];
}
