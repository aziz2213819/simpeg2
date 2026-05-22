<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $fillable = [
        'nama_petugas',
        'jenis_kendaraan',
        'plat_nomor',
        'tps_id',
    ];

    public function tps()
    {
        return $this->belongsTo(Tps::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'worker_id');
    }
}
