<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kolaborasi;
use Carbon\Carbon;

class KolaborasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Kolaborasi::all();
        return view('layouts_backend.kolaborasi.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $readonly = false;
        return view('layouts_backend.kolaborasi.create', compact('readonly'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string',
            'kategori' => 'required|string',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|file|max:2048'
        ]);

        $namaFile = 'default.jpg';

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');

            if ($file->isValid()) {
                $ext = strtolower($file->getClientOriginalExtension());

                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $namaFile = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('upload/kolaborasi'), $namaFile);
                }
            }
        }

        Kolaborasi::create([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'gambar' => $namaFile,
        ]);

        return redirect()->route('kolaborasi.index')->with('success', 'Data berhasil disimpan');
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
        $data = Kolaborasi::findOrFail($id);
        $readonly = false;
        return view('layouts_backend.kolaborasi.edit', compact('data', 'readonly'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Kolaborasi::findOrFail($id);

        $request->validate([
            'judul' => 'required|string',
            'kategori' => 'required|string',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|file|max:2048'
        ]);

        $namaFile = $data->gambar;

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');

            if ($file->isValid()) {
                $ext = strtolower($file->getClientOriginalExtension());

                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {

                    $namaFile = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('upload/kolaborasi'), $namaFile);

                    if ($data->gambar && file_exists(public_path('upload/kolaborasi/' . $data->gambar))) {
                        unlink(public_path('upload/kolaborasi/' . $data->gambar));
                    }
                }
            }
        }

        $data->update([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'gambar' => $namaFile,
        ]);

        return redirect()->route('kolaborasi.index')->with('success', 'Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function confirmDelete($id)
    {
        $data = Kolaborasi::findOrFail($id);
        $readonly = true;
        return view('layouts_backend.kolaborasi.delete', compact('data', 'readonly'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Kolaborasi::findOrFail($id);

        if ($data->gambar && file_exists(public_path('upload/kolaborasi/' . $data->gambar))) {
            unlink(public_path('upload/kolaborasi/' . $data->gambar));
        }

        $data->delete();

        return redirect()->route('kolaborasi.index')->with('success', 'Data dihapus');
    }
}
