<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InfoStatistik;
use Carbon\Carbon;

class InfoStatistikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = InfoStatistik::all();
        return view('layouts_backend.info_statistik.index', compact('data'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $readonly = false;
        return view('layouts_backend.info_statistik.create', compact('readonly'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_data' => 'required|string',
            'jumlah' => 'required|string',
            'keterangan' => 'required|string',
        ]);

        InfoStatistik::create($request->all());

        return redirect()->route('info_statistik.index')->with('success', 'Data Berhasil Disimpan');
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
    public function edit($id)
    {
        $data = InfoStatistik::findOrFail($id);
        $readonly = false;

        return view('layouts_backend.info_statistik.edit', compact('data', 'readonly'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = InfoStatistik::findOrFail($id);

        $request->validate([
            'jenis_data' => 'required|string',
            'jumlah' => 'required|string',
            'keterangan' => 'required|string',
        ]);

        $data->update($request->all());

        return redirect()->route('info_statistik.index')->with('success', 'Data Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function confirmDelete($id)
    {
        $data = InfoStatistik::findOrFail($id);
        $readonly = true;

        return view('layouts_backend.info_statistik.delete', compact('data', 'readonly'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = InfoStatistik::findOrFail($id);

        $data->delete();

        return redirect()->route('info_statistik.index')->with('success', 'Data Berhasil Dihapus');
    }
}
