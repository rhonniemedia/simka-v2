<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Jurusan;
use App\Models\Pegawai;
use App\Models\JenisPegawai;
use Illuminate\Http\Request;
use App\Models\StatusPegawai;
use App\Traits\HtmxResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class PensiunController extends Controller
{
    use HtmxResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pegawai::with(['statusPegawai', 'jenisPegawai', 'jabatan', 'jurusan'])
            ->where('status', 'pensiun')
            ->orderByDesc('tmt_status');

        // Search functionality - hash akan dihandle di model
        if ($request->filled('search')) {
            $search = $request->search;
            $query->search($search);
        }

        // 4. Filter Status Mutasi (Parameter 'status' dari JS)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        $pegawais = $query->paginate($perPage)->withQueryString();

        // If HTMX request, return only table partial
        if ($request->header('HX-Request')) {
            return view('contents.pensiun.partials.table', compact('pegawais'));
        }

        // Get filter options
        $statusPegawais = StatusPegawai::orderBy('nama')->get();
        $jenisPegawais = JenisPegawai::orderBy('nama')->get();
        $jabatans = Jabatan::orderBy('nama')->get();
        $jurusans = Jurusan::orderBy('nama')->get();

        return view('contents.pensiun.index', compact(
            'pegawais',
            'statusPegawais',
            'jenisPegawais',
            'jabatans',
            'jurusans',
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // PERBAIKAN: Ambil kolom 'nama' atau 'name' sesuai dengan struktur database Anda
        // Gunakan 'nama' jika kolom di database bernama 'nama'
        $daftarPegawais = Pegawai::select('id', 'nama', 'peg_slug')
            ->where('status', 'aktif') // Hanya pegawai aktif yang bisa dimutasi
            ->orderBy('nama')
            ->get();

        return view('contents.pensiun.partials.form', [
            'retirement' => new Pegawai(), // Objek kosong
            'daftarPegawais' => $daftarPegawais
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Kita tetap butuh ini untuk dilempar kembali ke view jika validasi gagal
        $daftarPegawais = Pegawai::select('nama', 'peg_slug')->get();

        try {
            $validated = $request->validate([
                'peg_slug'   => 'required|string',
                'status'     => 'required|in:pensiun',
                'tmt_status' => 'required|date',
            ]);

            $pegawai = Pegawai::where('peg_slug', $validated['peg_slug'])->firstOrFail();
            $pegawai->update([
                'status'     => $validated['status'],
                'tmt_status' => $validated['tmt_status'],
            ]);

            return $this->successResponse('pensiunSaved', 'Status pensiun berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Menggunakan trait untuk render ulang form saat validasi gagal
            return $this->validationErrorResponse(
                new Pegawai(),
                $e,
                'contents.pensiun.partials.form',
                'retirements',
                ['daftarPegawais' => $daftarPegawais]
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
