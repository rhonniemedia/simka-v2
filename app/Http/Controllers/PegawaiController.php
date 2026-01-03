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
        $draftCount = Pegawai::where('status', 'draft')->count();

        $query = Pegawai::with(['statusPegawai', 'jenisPegawai', 'jabatan', 'jurusan'])
            ->where('status', '!=', 'draft');

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
            'draftCount',
            'statusPegawais',
            'jenisPegawais',
            'jabatans',
            'jurusans'
        ));
    }

    /**
     * STEP 0: Membuka Modal Utama (Container)
     */
    public function create()
    {
        return view('contents.pegawai.partials.form-container', [
            'pegawai' => new Pegawai(),
        ]);
    }

    /**
     * STEP 1: Simpan Awal (Create Draft)
     */
    public function storeStep1(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'jk' => 'required|in:L,P',
                'agama' => 'required',
                't_lahir' => 'required',
                'tgl_lahir' => 'required|date',
                'nik' => 'required|digits:16|unique:pegawais,nik_hash',
                'kawin_tanggungan' => 'required|string',
            ]);

            // Tambahkan status draft secara manual
            $validated['status'] = 'draft';

            // Simpan ke database dan tampung ke variabel $pegawai
            $pegawai = Pegawai::create($validated);

            // Jika ini request HTMX, kita langsung kirim view Step 2
            // Anda bisa menyisipkan header untuk notifikasi sukses jika perlu
            return view('contents.pegawai.partials.form-step2', [
                'pegawai' => $pegawai,
                'statusPegawais' => StatusPegawai::all(),
                'jenisPegawais' => JenisPegawai::all(),
                'jabatans' => Jabatan::all(),
                'jurusans' => Jurusan::all(),
            ]);
        } catch (ValidationException $e) {
            // Gunakan fungsi error handling Anda
            return $this->validationErrorResponse(new Pegawai(), $e, 'contents.pegawai.partials.form-step1', 'pegawai');
        }
    }

    /**
     * STEP 2 & 3: Update Progress Draft
     */
    public function updateStep(Request $request, $id, $nextStep)
    {
        // Ambil data pegawai berdasarkan ID yang dikirim dari hidden input
        $pegawai = Pegawai::findOrFail($request->pegawai_id);

        try {
            $validated = $request->validate([
                'sp_id' => 'required|exists:status_pegawais,id',
                'jp_id' => 'required|exists:jenis_pegawais,id',
                'jab_id' => 'required|exists:jabatans,id',
                'jurusan_id' => 'required|exists:jurusans,id',
                // Unik kecuali untuk ID pegawai ini sendiri agar tidak error saat update
                'nip' => 'nullable|digits:18|unique:pegawais,nip,' . $pegawai->id,
                'nuptk' => 'nullable|digits:16|unique:pegawais,nuptk,' . $pegawai->id,
                'pmk' => 'nullable|in:ya,tidak',
                'pmk_tmt' => 'required_if:pmk,ya|nullable|date',
                'pmk_thn' => 'required_if:pmk,ya|nullable|numeric|min:0',
                'pmk_bln' => 'required_if:pmk,ya|nullable|numeric|min:0|max:11',
            ]);

            $validated['pmk'] = $validated['pmk'] ?? 'tidak';

            // Update data pegawai
            $pegawai->update($validated);

            // Jika berhasil, kirim view Step 3 (misal: Data Keluarga atau Dokumen)
            return view('contents.pegawai.partials.form-step' . $nextStep, [
                'pegawai' => $pegawai,
                // Tambahkan data pendukung untuk step 3 jika ada
            ]);
        } catch (ValidationException $e) {
            // Jika validasi gagal, kembalikan ke form step 2 dengan pesan error
            // Kita perlu mengirimkan kembali data pendukung agar dropdown tidak kosong

            $prevStep = $nextStep - 1;

            return $this->validationErrorResponse(
                $pegawai,
                $e,
                'contents.pegawai.partials.form-step' . $prevStep,
                'pegawai',
                [
                    'statusPegawais' => StatusPegawai::all(),
                    'jenisPegawais' => JenisPegawai::all(),
                    'jabatans' => Jabatan::all(),
                    'jurusans' => Jurusan::all(),
                ]
            );
        }




        $pegawai = Pegawai::findOrFail($id);

        // Ambil aturan validasi berdasarkan step yang baru saja diisi
        $rules = $this->getValidationRules($nextStep - 1);
        $validated = $request->validate($rules);

        $pegawai->update($validated);

        return view("contents.pegawai.partials.form-step{$nextStep}", compact('pegawai'));
    }

    /**
     * STEP 4: Finalisasi (Draft -> Aktif)
     */
    public function finalize(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $validated = $request->validate($this->getValidationRules(4));

        // Update data terakhir dan ubah status menjadi aktif
        $pegawai->update(array_merge($validated, ['status' => 'aktif']));

        // Beri sinyal HTMX untuk menutup modal dan refresh tabel
        return response('', 200)
            ->header('HX-Trigger', 'pegawaiUpdated');
    }

    /**
     * Menampilkan daftar pegawai yang masih berstatus draf di dalam modal
     */
    public function listDrafts()
    {
        // Mengambil semua data pegawai dengan status draft
        $drafts = Pegawai::where('status', 'draft')
            ->latest()
            ->get();

        // Mengembalikan view partial yang berisi list draf
        return view('contents.pegawai.partials.draft-list', compact('drafts'));
    }

    /**
     * Fitur Lanjutkan: Mendeteksi draf mana yang harus dibuka
     */
    public function resume($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        if (!$pegawai->sp_id) {
            return view('contents.pegawai.partials.form-step2', [
                'pegawai' => $pegawai,
                'statusPegawais' => StatusPegawai::all(),
                'jenisPegawais' => JenisPegawai::all(),
                'jabatans' => Jabatan::all(),
                'jurusans' => Jurusan::all(),
            ]);
        }

        if (!$pegawai->nik_hash) {
            return view('contents.pegawai.partials.form-step3', compact('pegawai'));
        }

        return view('contents.pegawai.partials.form-step4', compact('pegawai'));
    }

    /**
     * Aturan Validasi per Step
     */
    private function getValidationRules($step)
    {
        return match ($step) {
            2 => [
                'sp_id' => 'required|uuid',
                'jp_id' => 'required|uuid',
                'jab_id' => 'required|uuid',
                'jurusan_id' => 'required|uuid',
                'pmk' => 'nullable|string',
            ],
            3 => [
                'nik' => 'required|numeric|digits:16',
                'nip' => 'nullable|numeric',
                'besaran_gaji' => 'nullable|numeric',
            ],
            4 => [
                'telepon' => 'required|string',
                'jalan' => 'required|string',
            ],
            default => [],
        };
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
