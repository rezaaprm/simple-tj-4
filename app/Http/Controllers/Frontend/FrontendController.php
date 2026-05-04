<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\InfoStatistik;
use App\Models\Destinasi;
use App\Models\Galeri;
use App\Models\Kolaborasi;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // digunakan untuk menampilkan view di browser
        $about = About::first();
        $statistik = InfoStatistik::orderBy('id_info_statistik')->get();
        $destinasi = Destinasi::all()->groupBy('kategori');
        $galeri = Galeri::all()->groupBy('kategori');
        $kolaborasi = Kolaborasi::latest()->get();

        return view('layouts_frontend.frontend', compact('about', 'statistik', 'destinasi', 'galeri', 'kolaborasi'));
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
