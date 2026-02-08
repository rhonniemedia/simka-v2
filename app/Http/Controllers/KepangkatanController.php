<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KepangkatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JenisPegawai::orderBy('nama');

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                ->orWhere('alias', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 10);
        $jenisPegawais = $query->paginate($perPage)->withQueryString();

        if ($request->header('HX-Request')) {
            return view('contents.master.kepegawaian.jenis.partials.table', compact('jenisPegawais'));
        }

        return view('contents.master.kepegawaian.index', compact('jenisPegawais'));
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
