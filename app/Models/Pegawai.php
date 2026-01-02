<?php

namespace App\Models;

use App\Models\Jurusan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pegawai extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pegawais';

    // UUID akan auto-generate
    public $incrementing = false;
    protected $keyType = 'string';

    // ===== ENCRYPTED FIELDS =====
    protected $encrypted = [
        'nip',
        'nik',
        'nuptk',
        'npwp',
        'no_rek',
        'besaran_gaji',
        'telepon',
        'email',
        'tgl_lahir',
        'agama',
        'jalan',
        'desa_kelurahan',
        'rt',
        'rw',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'kode_pos',
    ];

    // ===== FIELDS dengan HASH =====
    protected $hashed = [
        'nip',
        'nik',
        'nuptk',
        'npwp',
        'email',
        'telepon',
    ];

    protected $fillable = [
        'nama',
        'peg_slug',
        'sp_id',
        'jp_id',
        'jab_id',
        'nip',
        'nik',
        'nuptk',
        'npwp',
        'no_rek',
        'besaran_gaji',
        'pmk',
        'tmt_mk',
        't_lahir',
        'tgl_lahir',
        'jk',
        'agama',
        'kawin_tanggungan',
        'telepon',
        'email',
        'jalan',
        'desa_kelurahan',
        'rt',
        'rw',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'kode_pos',
        'foto',
        'jurusan',
        'status',
        'tmt_status'
    ];

    protected $casts = [
        'tmt_mk' => 'date',
        'tmt_status' => 'date',
    ];

    // ===== AUTO ENCRYPT/DECRYPT =====

    public function setAttribute($key, $value)
    {
        if (is_null($value) || $value === '') {
            if (in_array($key, $this->encrypted)) {
                $this->attributes[$key . '_encrypted'] = null;

                if (in_array($key, $this->hashed)) {
                    $this->attributes[$key . '_hash'] = null;
                }

                return $this;
            }
            return parent::setAttribute($key, $value);
        }

        if (in_array($key, $this->encrypted)) {
            // Encrypt data
            $this->attributes[$key . '_encrypted'] = Crypt::encryptString((string) $value);

            // Create hash untuk unique/searchable fields
            if (in_array($key, $this->hashed)) {
                $this->attributes[$key . '_hash'] = hash('sha256', (string) $value);
            }

            // Special: telepon → create masked
            if ($key === 'telepon') {
                $this->attributes['telepon_masked'] = $this->maskPhone($value);
            }

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    public function getAttribute($key)
    {
        if (in_array($key, $this->encrypted)) {
            $encryptedKey = $key . '_encrypted';

            if (isset($this->attributes[$encryptedKey]) && $this->attributes[$encryptedKey] !== null) {
                try {
                    return Crypt::decryptString($this->attributes[$encryptedKey]);
                } catch (\Exception $e) {
                    Log::error("Decrypt failed for {$key}: " . $e->getMessage());
                    return null;
                }
            }

            return null;
        }

        return parent::getAttribute($key);
    }

    protected function maskPhone($phone)
    {
        $phone = (string) $phone;
        if (strlen($phone) < 8) return $phone;

        $start = substr($phone, 0, 4);
        $end = substr($phone, -4);
        $middle = str_repeat('*', strlen($phone) - 8);

        return $start . $middle . $end;
    }

    // ===== ACCESSORS =====

    public function getTglLahirCarbonAttribute()
    {
        $tglLahir = $this->tgl_lahir;
        return $tglLahir ? \Carbon\Carbon::parse($tglLahir) : null;
    }

    public function getUmurAttribute()
    {
        $tglLahir = $this->tgl_lahir_carbon;
        return $tglLahir ? $tglLahir->age : null;
    }

    public function getAlamatLengkapAttribute()
    {
        $parts = array_filter([
            $this->jalan,
            $this->rt ? "RT {$this->rt}" : null,
            $this->rw ? "RW {$this->rw}" : null,
            $this->desa_kelurahan,
            $this->kecamatan,
            $this->kabupaten_kota,
            $this->provinsi,
            $this->kode_pos,
        ]);

        return implode(', ', $parts);
    }

    // ===== SCOPES =====

    public function scopeByNip($query, $nip)
    {
        return $query->where('nip_hash', hash('sha256', $nip));
    }

    public function scopeByNik($query, $nik)
    {
        return $query->where('nik_hash', hash('sha256', $nik));
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where('email_hash', hash('sha256', strtolower($email)));
    }

    public function scopeByNuptk($query, $nuptk)
    {
        return $query->where('nuptk_hash', hash('sha256', $nuptk));
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    // ===== RELATIONSHIPS =====

    public function jurusan()
    {
        // Adjust 'Jurusan' to your actual Model name 
        // and 'jurusan_id' to your actual foreign key
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function statusPegawai()
    {
        return $this->belongsTo(StatusPegawai::class, 'sp_id');
    }

    public function jenisPegawai()
    {
        return $this->belongsTo(JenisPegawai::class, 'jp_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jab_id');
    }

    public function spHistories()
    {
        return $this->hasMany(StatusPegawaiHistories::class, 'pegawai_id');
    }

    public function jabHistories()
    {
        return $this->hasMany(JabatanHistories::class, 'pegawai_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
