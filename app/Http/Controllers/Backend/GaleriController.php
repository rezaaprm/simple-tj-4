<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Galeri;
use Carbon\Carbon;

class GaleriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Galeri::all();
        return view('layouts_backend.galeri.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $readonly = false;
        return view('layouts_backend.galeri.create', compact('readonly'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string',
            'kategori' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $namaFile = 'default.jpg';

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');

            if ($file->isValid()) {
                $namaFile = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('upload/galeri'), $namaFile);
            }
        }

        Galeri::create([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'gambar' => $namaFile,
        ]);

        return redirect()->route('galeri.index')->with('success', 'Data berhasil disimpan');
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
        $data = Galeri::findOrFail($id);
        $readonly = false;
        return view('layouts_backend.galeri.edit', compact('data', 'readonly'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Galeri::findOrFail($id);

        $request->validate([
            'judul' => 'required|string',
            'kategori' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $namaFile = $data->gambar;

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');

            if ($file->isValid()) {
                $namaFile = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('upload/galeri'), $namaFile);
            }
        }

        $data->update([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'gambar' => $namaFile,
        ]);

        return redirect()->route('galeri.index')->with('success', 'Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function confirmDelete($id)
    {
        $data = Galeri::findOrFail($id);
        $readonly = true;
        return view('layouts_backend.galeri.delete', compact('data', 'readonly'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Galeri::findOrFail($id);
        $data->delete();

        return redirect()->route('galeri.index')->with('success', 'Data dihapus');
    }
}
