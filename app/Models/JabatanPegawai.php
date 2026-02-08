<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JabatanPegawai extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'jabatan_pegawais';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama',
        'kode',
        'status',
    ];

    protected $casts = [
        'is_struktural' => 'boolean',
    ];

    public function jenisPegawai()
    {
        return $this->belongsTo(JenisPegawai::class, 'jenis_pegawai_id');
    }

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'jab_id');
    }

    public function histories()
    {
        return $this->hasMany(JabatanHistories::class, 'jab_id');
    }
}
