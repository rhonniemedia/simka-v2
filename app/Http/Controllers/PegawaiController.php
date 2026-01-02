<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\StatusPegawai;
use App\Models\JenisPegawai;
use App\Models\Jabatan;
use App\Models\Jurusan;
use App\Traits\HtmxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PegawaiController extends Controller
{
    use HtmxResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pegawai::with(['statusPegawai', 'jenisPegawai', 'jabatan', 'jurusan']);

        // Search functionality - hash akan dihandle di model
        if ($request->filled('search')) {
            $search = $request->search;
            $query->search($search);
        }

        // Filter by status pegawai
        if ($request->filled('sp_id')) {
            $query->where('sp_id', $request->sp_id);
        }

        // Filter by jenis pegawai
        if ($request->filled('jp_id')) {
            $query->where('jp_id', $request->jp_id);
        }

        // Filter by jabatan
        if ($request->filled('jab_id')) {
            $query->where('jab_id', $request->jab_id);
        }

        // Filter by jurusan
        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        // Filter by status aktif
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Per page
        $perPage = $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $pegawais = $query->latest()->paginate($perPage)->withQueryString();

        // If HTMX request, return only table partial
        if ($request->header('HX-Request')) {
            return view('contents.pegawai.partials.table', compact('pegawais'));
        }

        // Get filter options
        $statusPegawais = StatusPegawai::orderBy('nama')->get();
        $jenisPegawais = JenisPegawai::orderBy('nama')->get();
        $jabatans = Jabatan::orderBy('nama')->get();
        $jurusans = Jurusan::orderBy('nama')->get();

        return view('contents.pegawai.index', compact(
            'pegawais',
            'statusPegawais',
            'jenisPegawais',
            'jabatans',
            'jurusans'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statusPegawais = StatusPegawai::orderBy('nama')->get();
        $jenisPegawais = JenisPegawai::orderBy('nama')->get();
        $jabatans = Jabatan::orderBy('nama')->get();
        $jurusans = Jurusan::orderBy('nama')->get();

        return view('contents.pegawai.partials.form', [
            'pegawai' => new Pegawai(),
            'statusPegawais' => $statusPegawais,
            'jenisPegawais' => $jenisPegawais,
            'jabatans' => $jabatans,
            'jurusans' => $jurusans
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'sp_id' => 'required|uuid|exists:status_pegawais,id',
                'jp_id' => 'required|uuid|exists:jenis_pegawais,id',
                'jab_id' => 'required|uuid|exists:jabatans,id',
                'jurusan_id' => 'required|uuid|exists:jurusans,id',

                // Nomor Identitas
                'nip' => 'nullable|string|max:50',
                'nik' => 'required|string|max:16',
                'nuptk' => 'nullable|string|max:50',
                'npwp' => 'nullable|string|max:50',

                // Data Finansial
                'no_rek' => 'nullable|string|max:50',
                'besaran_gaji' => 'nullable|numeric|min:0',

                // Data Kepegawaian
                'pmk' => 'nullable|string|max:50',
                'tmt_mk' => 'nullable|date',

                // Data Pribadi
                't_lahir' => 'required|string|max:100',
                'tgl_lahir' => 'required|date',
                'jk' => 'required|in:L,P',
                'agama' => 'required|string|max:50',
                'kawin_tanggungan' => 'nullable|string|max:50',

                // Kontak
                'telepon' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',

                // Alamat
                'jalan' => 'required|string|max:255',
                'desa_kelurahan' => 'required|string|max:100',
                'rt' => 'nullable|string|max:5',
                'rw' => 'nullable|string|max:5',
                'kecamatan' => 'required|string|max:100',
                'kabupaten_kota' => 'required|string|max:100',
                'provinsi' => 'required|string|max:100',
                'kode_pos' => 'nullable|string|max:10',

                // File & Status
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status' => 'required|in:aktif,mutasi,pensiun',
                'tmt_status' => 'nullable|date',
            ]);

            // Upload Foto
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $fotoName = time() . '_' . Str::slug($validated['nama']) . '.' . $foto->getClientOriginalExtension();
                $foto->storeAs('public/pegawai/foto', $fotoName);
                $validated['foto'] = 'pegawai/foto/' . $fotoName;
            }

            // Audit
            $validated['created_by'] = auth()->id();

            // Create pegawai - enkripsi dan hashing akan dihandle di model
            Pegawai::create($validated);

            return $this->successResponse('pegawaiSaved', 'Data pegawai berhasil ditambahkan.');
        } catch (ValidationException $e) {
            $statusPegawais = StatusPegawai::orderBy('nama')->get();
            $jenisPegawais = JenisPegawai::orderBy('nama')->get();
            $jabatans = Jabatan::orderBy('nama')->get();
            $jurusans = Jurusan::orderBy('nama')->get();

            return $this->validationErrorResponse(
                new Pegawai(),
                $e,
                'contents.pegawai.partials.form',
                'pegawai',
                compact('statusPegawais', 'jenisPegawais', 'jabatans', 'jurusans')
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pegawai $pegawai)
    {
        $pegawai->load(['statusPegawai', 'jenisPegawai', 'jabatan', 'jurusan', 'creator', 'updater']);

        return view('contents.pegawai.partials.detail', compact('pegawai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pegawai $pegawai)
    {
        $statusPegawais = StatusPegawai::orderBy('nama')->get();
        $jenisPegawais = JenisPegawai::orderBy('nama')->get();
        $jabatans = Jabatan::orderBy('nama')->get();
        $jurusans = Jurusan::orderBy('nama')->get();

        return view('contents.pegawai.partials.form', compact(
            'pegawai',
            'statusPegawais',
            'jenisPegawais',
            'jabatans',
            'jurusans'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'sp_id' => 'required|uuid|exists:status_pegawais,id',
                'jp_id' => 'required|uuid|exists:jenis_pegawais,id',
                'jab_id' => 'required|uuid|exists:jabatans,id',
                'jurusan_id' => 'required|uuid|exists:jurusans,id',

                // Nomor Identitas
                'nip' => 'nullable|string|max:50',
                'nik' => 'required|string|max:16',
                'nuptk' => 'nullable|string|max:50',
                'npwp' => 'nullable|string|max:50',

                // Data Finansial
                'no_rek' => 'nullable|string|max:50',
                'besaran_gaji' => 'nullable|numeric|min:0',

                // Data Kepegawaian
                'pmk' => 'nullable|string|max:50',
                'tmt_mk' => 'nullable|date',

                // Data Pribadi
                't_lahir' => 'required|string|max:100',
                'tgl_lahir' => 'required|date',
                'jk' => 'required|in:L,P',
                'agama' => 'required|string|max:50',
                'kawin_tanggungan' => 'nullable|string|max:50',

                // Kontak
                'telepon' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',

                // Alamat
                'jalan' => 'required|string|max:255',
                'desa_kelurahan' => 'required|string|max:100',
                'rt' => 'nullable|string|max:5',
                'rw' => 'nullable|string|max:5',
                'kecamatan' => 'required|string|max:100',
                'kabupaten_kota' => 'required|string|max:100',
                'provinsi' => 'required|string|max:100',
                'kode_pos' => 'nullable|string|max:10',

                // File & Status
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status' => 'required|in:aktif,mutasi,pensiun',
                'tmt_status' => 'nullable|date',
            ]);

            // Upload Foto baru
            if ($request->hasFile('foto')) {
                // Delete old foto
                if ($pegawai->foto) {
                    Storage::delete('public/' . $pegawai->foto);
                }

                $foto = $request->file('foto');
                $fotoName = time() . '_' . Str::slug($validated['nama']) . '.' . $foto->getClientOriginalExtension();
                $foto->storeAs('public/pegawai/foto', $fotoName);
                $validated['foto'] = 'pegawai/foto/' . $fotoName;
            }

            // Audit
            $validated['updated_by'] = auth()->id();

            // Update pegawai - enkripsi dan hashing akan dihandle di model
            $pegawai->fill($validated);

            if (!$pegawai->isDirty()) {
                return $this->infoResponse('Tidak Ada Perubahan', 'Data tetap sama.', 'pegawaiUpdated');
            }

            $pegawai->save();

            return $this->successResponse('pegawaiUpdated', 'Data pegawai berhasil diperbarui.');
        } catch (ValidationException $e) {
            $statusPegawais = StatusPegawai::orderBy('nama')->get();
            $jenisPegawais = JenisPegawai::orderBy('nama')->get();
            $jabatans = Jabatan::orderBy('nama')->get();
            $jurusans = Jurusan::orderBy('nama')->get();

            return $this->validationErrorResponse(
                $pegawai,
                $e,
                'contents.pegawai.partials.form',
                'pegawai',
                compact('statusPegawais', 'jenisPegawais', 'jabatans', 'jurusans')
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();

        return $this->successResponse('pegawaiUpdated', 'Data pegawai berhasil dihapus.');
    }
}
