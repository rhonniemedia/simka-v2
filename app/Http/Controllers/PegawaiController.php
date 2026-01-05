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
                't_lahir' => 'required|string|max:255',
                'tgl_lahir' => 'required|date',
                'nik' => [
                    'required',
                    'digits:16',
                    // Validasi unique menggunakan scope dari model
                    function ($attribute, $value, $fail) {
                        if (Pegawai::byNik($value)->exists()) {
                            $fail('NIK sudah terdaftar.');
                        }
                    },
                ],
                'kawin_tanggungan' => 'required|string',
                'npwp' => [
                    'nullable',
                    'string',
                    'regex:/^[0-9]{15,16}$/',
                    function ($attribute, $value, $fail) {
                        if (Pegawai::byNpwp($value)->exists()) {
                            $fail('NPWP sudah terdaftar.');
                        }
                    },
                ],
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Handle upload foto jika ada
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $fotoName = time() . '_' . $foto->getClientOriginalName();
                $foto->storeAs('public/pegawai', $fotoName);
                $validated['foto'] = $fotoName;
            }

            // Tambahkan status draft
            $validated['status'] = 'draft';

            // Simpan ke database
            // Model akan otomatis hash NIK melalui setAttribute()
            $pegawai = Pegawai::create($validated);

            // Return view Step 2 dengan data lengkap
            return view('contents.pegawai.partials.form-step2', [
                'pegawai' => $pegawai,
                'statusPegawais' => StatusPegawai::all(),
                'jenisPegawais' => JenisPegawai::all(),
                'jabatans' => Jabatan::all(),
                'jurusans' => Jurusan::all(),
            ]);
        } catch (ValidationException $e) {
            // PENTING: Gunakan trait HtmxResponse yang sudah ada
            return $this->validationErrorResponse(
                new Pegawai(),
                $e,
                'contents.pegawai.partials.form-step1',
                'pegawai'
            );
        }
    }

    /**
     * STEP 2 & 3: Update Progress Draft (Dynamic)
     */
    public function updateStep(Request $request, $id, $nextStep)
    {
        // Ambil data pegawai berdasarkan ID yang dikirim dari hidden input
        $pegawai = Pegawai::findOrFail($request->pegawai_id);

        // Step yang sedang diisi (sebelum next)
        $currentStep = $nextStep - 1;

        try {
            // Dapatkan aturan validasi berdasarkan step saat ini
            $rules = $this->getValidationRulesForStep($currentStep, $pegawai);

            $validated = $request->validate($rules);

            // Handle default value untuk field tertentu
            if ($currentStep == 2 && !isset($validated['pmk'])) {
                $validated['pmk'] = 'tidak';
            }

            // Update data pegawai
            $pegawai->update($validated);

            // Dapatkan view data untuk step berikutnya
            $viewData = $this->getViewDataForStep($nextStep, $pegawai);

            // Return view untuk step berikutnya
            return view('contents.pegawai.partials.form-step' . $nextStep, $viewData);
        } catch (ValidationException $e) {
            // Dapatkan view data untuk step saat ini (yang error)
            $viewData = $this->getViewDataForStep($currentStep, $pegawai);

            return $this->validationErrorResponse(
                $pegawai,
                $e,
                'contents.pegawai.partials.form-step' . $currentStep,
                'pegawai',
                $viewData
            );
        }
    }

    /**
     * Dapatkan aturan validasi berdasarkan step
     */
    private function getValidationRulesForStep(int $step, Pegawai $pegawai): array
    {
        return match ($step) {
            // Step 2: Data Kepegawaian
            2 => [
                'sp_id' => 'required|exists:status_pegawais,id',
                'jp_id' => 'required|exists:jenis_pegawais,id',
                'jab_id' => 'required|exists:jabatans,id',
                'jurusan_id' => 'required|exists:jurusans,id',

                // Validasi NIP dengan custom rule untuk hash
                'nip' => [
                    'nullable',
                    'digits:18',
                    function ($attribute, $value, $fail) use ($pegawai) {
                        if (!$value) return;

                        $exists = Pegawai::byNip($value)
                            ->where('id', '!=', $pegawai->id)
                            ->exists();

                        if ($exists) {
                            $fail('NIP sudah terdaftar.');
                        }
                    },
                ],

                // Validasi NUPTK dengan custom rule untuk hash
                'nuptk' => [
                    'nullable',
                    'digits:16',
                    function ($attribute, $value, $fail) use ($pegawai) {
                        if (!$value) return;

                        $exists = Pegawai::byNuptk($value)
                            ->where('id', '!=', $pegawai->id)
                            ->exists();

                        if ($exists) {
                            $fail('NUPTK sudah terdaftar.');
                        }
                    },
                ],

                'pmk' => 'nullable|in:ya,tidak',
                'pmk_tmt' => 'required_if:pmk,ya|nullable|date',
                'pmk_thn' => 'required_if:pmk,ya|nullable|numeric|min:0',
                'pmk_bln' => 'required_if:pmk,ya|nullable|numeric|min:0|max:11',
            ],

            // Step 3: Data Alamat
            3 => [
                'jalan' => 'nullable|string|max:255',
                'rt' => 'nullable|string|max:5',
                'rw' => 'nullable|string|max:5',
                'desa_kelurahan' => 'required|string|max:100',
                'kecamatan' => 'required|string|max:100',
                'kabupaten_kota' => 'required|string|max:100',
                'provinsi' => 'required|string|max:100',
                'kode_pos' => 'nullable|string|max:10',
            ],

            // Step 4: Data Kontak & Finalisasi
            4 => [
                // Telepon Utama (Required)
                'telepon' => 'required|string|max:20',

                // TMT Status (Required)
                'tmt_status' => 'required|date',

                // Telepon Alternatif (Optional)
                'telepon_alternatif' => 'nullable|string|max:20',

                // Email Utama (Required) dengan validasi unique
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    function ($attribute, $value, $fail) use ($pegawai) {
                        $exists = Pegawai::byEmail($value)
                            ->where('id', '!=', $pegawai->id)
                            ->exists();

                        if ($exists) {
                            $fail('Email utama sudah terdaftar.');
                        }
                    },
                ],

                // Email Alternatif (Optional) dengan validasi unique
                'email_alternatif' => [
                    'nullable',
                    'email',
                    'max:255',
                    function ($attribute, $value, $fail) use ($pegawai) {
                        if (!$value) return;

                        // Cek jika email alternatif sama dengan email utama
                        if (request('email') && $value === request('email')) {
                            $fail('Email alternatif tidak boleh sama dengan email utama.');
                            return;
                        }

                        $exists = Pegawai::byEmail($value)
                            ->where('id', '!=', $pegawai->id)
                            ->exists();

                        if ($exists) {
                            $fail('Email alternatif sudah terdaftar.');
                        }
                    },
                ],
            ],

            default => [],
        };
    }

    /**
     * Dapatkan data tambahan untuk view berdasarkan step
     */
    private function getViewDataForStep(int $step, Pegawai $pegawai): array
    {
        $data = ['pegawai' => $pegawai];

        // Step 2 membutuhkan dropdown options
        if ($step == 2) {
            $data['statusPegawais'] = StatusPegawai::all();
            $data['jenisPegawais'] = JenisPegawai::all();
            $data['jabatans'] = Jabatan::all();
            $data['jurusans'] = Jurusan::all();
        }

        // Step 3 tidak butuh data tambahan
        // Step 4 mungkin butuh data tambahan di masa depan

        return $data;
    }

    /**
     * STEP 4: Finalisasi (Draft -> Aktif)
     */
    public function finalize(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        try {
            $validated = $request->validate(
                $this->getValidationRulesForStep(4, $pegawai)
            );

            // Update data terakhir dan ubah status menjadi aktif
            $pegawai->update(array_merge($validated, [
                'status' => 'aktif',
                'tmt_status' => $validated['tmt_status'] ?? now(),
            ]));

            // Gunakan successResponse seperti di product
            return $this->successResponse('pegawaiUpdated', 'Data pegawai berhasil disimpan dan diaktifkan.');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse(
                $pegawai,
                $e,
                'contents.pegawai.partials.form-step4',
                'pegawai'
            );
        }
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
}
