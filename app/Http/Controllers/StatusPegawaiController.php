<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Jurusan;
use App\Models\JenisPegawai;
use Illuminate\Http\Request;
use App\Models\StatusPegawai;

class StatusPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get filter options
        $statusPegawais = StatusPegawai::orderBy('nama')->paginate(10);
        $jenisPegawais = JenisPegawai::orderBy('nama')->get();
        $jabatans = Jabatan::orderBy('nama')->get();
        $jurusans = Jurusan::orderBy('nama')->get();

        return view('contents.master.kepegawaian.index', compact(
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StatusPegawai $statusPegawai)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StatusPegawai $statusPegawai)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StatusPegawai $statusPegawai)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StatusPegawai $statusPegawai)
    {
        //
    }
}
