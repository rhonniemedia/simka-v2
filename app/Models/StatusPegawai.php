<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StatusPegawai extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'status_pegawais';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = ['id'];

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'sp_id');
    }

    public function histories()
    {
        return $this->hasMany(StatusPegawaiHistories::class, 'sp_id');
    }
}
