<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TpsReport extends Model
{
    protected $fillable = [
        'worker_id',
        'tps_id',
        'status_angkut',
        'keterangan',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function tps()
    {
        return $this->belongsTo(Tps::class);
    }
}
