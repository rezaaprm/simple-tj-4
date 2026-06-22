<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Destinasi;

class DestinasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Destinasi::all();
        return view('layouts_backend.destinasi.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $readonly = false;
        return view('layouts_backend.destinasi.create', compact('readonly'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'kategori' => 'required|string',
            // bebas file tipe apa apapun supaya mencegah error
            'gambar' => 'nullable|file|max:2048'
        ]);

        // default gambar
        $namaFile = 'default.jpg';

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');

            if ($file->isValid()) {
                $ext = strtolower($file->getClientOriginalExtension());

                // HANYA TERIMA JPG/PNG
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {

                    $namaFile = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('upload/destinasi'), $namaFile);
                }

                // file tipe selain itu (webp dll) akan DIABAIKAN TANPA ERROR
            }
        }

        Destinasi::create([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'gambar' => $namaFile,
        ]);

        return redirect()->route('destinasi.index')->with('success', 'Data berhasil disimpan');
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
        $data = Destinasi::findOrFail($id);
        $readonly = false;
        return view('layouts_backend.destinasi.edit', compact('data', 'readonly'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Destinasi::findOrFail($id);

        $request->validate([
            'nama' => 'required|string',
            'kategori' => 'required|string',
            // tidak wajib & bebas file
            'gambar' => 'nullable|file|max:2048'
        ]);

        // default = gambar lama
        $namaFile = $data->gambar;

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');

            if ($file->isValid()) {
                $ext = strtolower($file->getClientOriginalExtension());

                // hanya jpg/png
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {

                    $namaFile = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('upload/destinasi'), $namaFile);

                    // hapus gambar lama
                    if ($data->gambar && file_exists(public_path('upload/destinasi/' . $data->gambar))) {
                        unlink(public_path('upload/destinasi/' . $data->gambar));
                    }
                }

                // webp dll → DIABAIKAN
            }
        }

        $data->update([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'gambar' => $namaFile,
        ]);

        return redirect()->route('destinasi.index')->with('success', 'Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function confirmDelete($id)
    {
        $data = Destinasi::findOrFail($id);
        $readonly = true;
        return view('layouts_backend.destinasi.delete', compact('data', 'readonly'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Destinasi::findOrFail($id);

        // hapus file gambar
        if ($data->gambar && file_exists(public_path('upload/destinasi/' . $data->gambar))) {
            unlink(public_path('upload/destinasi/' . $data->gambar));
        }

        $data->delete();

        return redirect()->route('destinasi.index')->with('success', 'Data berhasil dihapus');
    }
}
