<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StatusPegawai;
use App\Traits\HtmxResponse; // Pastikan Trait di-import
use Illuminate\Validation\ValidationException;

class StatusPegawaiController extends Controller
{
    use HtmxResponse; // Gunakan trait global

    public function index(Request $request)
    {
        $query = StatusPegawai::orderBy('created_at');

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                ->orWhere('alias', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 10);
        $statusPegawais = $query->paginate($perPage)->withQueryString();

        if ($request->header('HX-Request')) {
            return view('contents.master.kepegawaian.status.partials.table', compact('statusPegawais'));
        }

        return view('contents.master.kepegawaian.index', compact('statusPegawais'));
    }

    public function create()
    {
        return view('contents.master.kepegawaian.status.partials.form', [
            'status' => new StatusPegawai()
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'nama'   => 'required|string|max:50|unique:status_pegawais,nama',
                    'alias'  => 'required|string|max:255',
                    'status' => 'required|in:aktif,arsip',
                ],
                [
                    'nama.required' => 'Nama status pegawai wajib diisi.',
                    'nama.unique'   => 'Nama status pegawai sudah ada, silakan gunakan nama lain.',

                    'alias.required' => 'Alias wajib diisi.',

                    'status.required' => 'Status wajib dipilih.',
                ]
            );

            StatusPegawai::create($validated);

            // 'statusUpdated' akan ditangkap oleh core-app.js untuk tutup modal & refresh tabel
            return $this->successResponse('statusUpdated', 'Status pegawai berhasil disimpan.');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse(
                new StatusPegawai(),
                $e,
                'contents.master.kepegawaian.status.partials.form',
                'status'
            );
        }
    }

    /**
     * Show form for editing status pegawai
     */
    public function edit(StatusPegawai $statusPegawai)
    {
        // ✅ Return form view dengan data yang akan di-edit
        return view('contents.master.kepegawaian.status.partials.form', [
            'status' => $statusPegawai
        ]);
    }

    /**
     * Update existing status pegawai
     */
    public function update(Request $request, StatusPegawai $statusPegawai)
    {
        try {
            $validated = $request->validate([
                'nama'   => 'required|string|max:50|unique:status_pegawais,nama,' . $statusPegawai->id,
                'alias'  => 'required|string|max:255',
                'status' => 'required|in:aktif,arsip',
            ]);

            // 1. Isi model dengan data baru (belum disimpan ke DB)
            $statusPegawai->fill($validated);

            // 2. Cek apakah ada atribut yang berubah
            if (!$statusPegawai->isDirty()) {
                // ✅ PERBAIKAN: Urutan parameter disesuaikan dengan Trait (Title, Message, Event)
                return $this->infoResponse(
                    'Info',                                     // Parameter 1: Title
                    'Tidak ada perubahan data yang disimpan.',  // Parameter 2: Message
                    'statusUpdated'                             // Parameter 3: Event Trigger
                );
            }

            // 3. Jika ada perubahan, simpan
            $statusPegawai->save();

            return $this->successResponse('statusUpdated', 'Status kepegawaian berhasil diperbarui.');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse(
                $statusPegawai,
                $e,
                'contents.master.kepegawaian.status.partials.form',
                'status'
            );
        }
    }

    public function destroy(StatusPegawai $statusPegawai)
    {
        $statusPegawai->delete();
        return $this->successResponse('statusUpdated', 'Status kepegawaian berhasil dihapus.');
    }
}
