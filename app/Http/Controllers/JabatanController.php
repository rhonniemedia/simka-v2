<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HtmxResponse;
use App\Models\JabatanPegawai;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JabatanPegawai::orderBy('created_at');

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                ->orWhere('kode', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 10);
        $jabatanPegawais = $query->paginate($perPage)->withQueryString();

        if ($request->header('HX-Request')) {
            return view('contents.master.kepegawaian.jabatan.partials.table', compact('jabatanPegawais'));
        }

        return view('contents.master.kepegawaian.index', compact('jabatanPegawais'));
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
    public function show(JabatanPegawai $jabatan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JabatanPegawai $jabatan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JabatanPegawai $jabatan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JabatanPegawai $jabatan)
    {
        //
    }
}
