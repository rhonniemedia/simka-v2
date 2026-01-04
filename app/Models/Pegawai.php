<?php

namespace App\Models;

use App\Models\Jurusan;
use Illuminate\Support\Str;
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
        'telepon_alternatif',
        'email',
        'email_alternatif',
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
        'email_alternatif',
        'telepon',
        'telepon_alternatif',
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
        'pmk_tmt',
        'pmk_thn',
        'pmk_bln',
        't_lahir',
        'tgl_lahir',
        'jk',
        'agama',
        'kawin_tanggungan',
        'telepon',
        'telepon_alternatif',
        'email',
        'email_alternatif',
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
        'pmk_tmt' => 'date',
        'tmt_status' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($pegawai) {
            // Membuat slug otomatis dari nama + random string agar unik
            if (empty($pegawai->peg_slug)) {
                $pegawai->peg_slug = Str::slug($pegawai->nama) . '-' . Str::lower(Str::random(5));
            }
        });
    }

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

    /**
     * Scope untuk pencarian pegawai
     * Disesuaikan untuk data yang terenkripsi
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            // ✅ 1. Search by NAMA (tidak terenkripsi)
            $q->where('nama', 'like', "%{$search}%");

            // ✅ 2. Search by T_LAHIR (tempat lahir - tidak terenkripsi)
            $q->orWhere('t_lahir', 'like', "%{$search}%");

            // ✅ 3. Search by PMK (tidak terenkripsi)
            $q->orWhere('pmk', 'like', "%{$search}%");

            // ✅ 4. Search by HASH fields (untuk exact match)
            // Cek apakah search input adalah nomor yang valid
            if (is_numeric(str_replace(['-', ' ', '.'], '', $search))) {
                $cleanSearch = str_replace(['-', ' ', '.'], '', $search);
                $searchHash = hash('sha256', $cleanSearch);

                $q->orWhere('nip_hash', $searchHash)
                    ->orWhere('nik_hash', $searchHash)
                    ->orWhere('nuptk_hash', $searchHash)
                    ->orWhere('npwp_hash', $searchHash)
                    ->orWhere('telepon_hash', $searchHash);
            }

            // ✅ 5. Search by EMAIL hash (case-insensitive)
            if (filter_var($search, FILTER_VALIDATE_EMAIL)) {
                $emailHash = hash('sha256', strtolower($search));
                $q->orWhere('email_hash', $emailHash);
            }

            // ✅ 6. Search by TELEPON MASKED (partial match)
            $q->orWhere('telepon_masked', 'like', "%{$search}%");

            // ✅ 7. Search by RELASI (tidak terenkripsi)
            // Status Pegawai
            $q->orWhereHas('statusPegawai', function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%");
            })

                // Jenis Pegawai
                ->orWhereHas('jenisPegawai', function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%");
                })

                // Jabatan
                ->orWhereHas('jabatan', function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%");
                })

                // Jurusan
                ->orWhereHas('jurusan', function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%");
                });
        });
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
