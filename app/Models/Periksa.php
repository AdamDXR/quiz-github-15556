<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periksa extends Model
{
    protected $table = 'periksa';

    protected $fillable = [
        'id_daftar_poli',
        'tgl_periksa',
        'catatan',
        'biaya_periksa',
        'bukti_pembayaran',
        'status_pembayaran',
        'tgl_pembayaran',
        'tgl_verifikasi',
    ];

    protected $casts = [
        'tgl_periksa' => 'datetime',
        'tgl_pembayaran' => 'datetime',
        'tgl_verifikasi' => 'datetime',
    ];

    public function daftarPoli()
    {
        return $this->belongsTo(DaftarPoli::class, 'id_daftar_poli');
    }

    public function detailPeriksas()
    {
        return $this->hasMany(DetailPeriksa::class, 'id_periksa');
    }
}