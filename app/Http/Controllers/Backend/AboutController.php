<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\About;
use Carbon\Carbon;

class AboutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = About::all();
        return view('layouts_backend.about.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $readonly = false;
        return view('layouts_backend.about.create', compact('readonly'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'judul'  => 'required|string',
            'deskripsi'  => 'required|string',
            'keterangan'  => 'required|string',
        ]);
        $validation = $request->all();

        About::create($validation);
        return redirect()->route('about.index')->with('success', 'Data Berhasil Disimpan');
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
        $data = About::findOrFail($id);
        $readonly = false;
        return view('layouts_backend.about.edit', compact('data', 'readonly'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $data = About::findOrFail($id);

        $request->validate([
            'judul'  => 'required|string|',
            'deskripsi'  => 'required|string',
            'keterangan'  => 'required|string',
        ]);
        $validation = $request->all();

        $data->update($validation);
        return redirect()->route('about.index')->with('success', 'Data Berhasil Disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function confirmDelete($id)
    {
        $data = About::findOrFail($id);
        $readonly = true;
        return view('layouts_backend.about.delete', compact('data', 'readonly'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $data = About::findOrFail($id);
        $data->update([
            'deleted_at'    => Carbon::now(),
        ]);
        return redirect()->route('about.index')->with('success', 'Data Berhasil Dihapus');
    }
}
