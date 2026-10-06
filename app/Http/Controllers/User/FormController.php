<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\Layanan;
use App\Models\Admin\FormCategory;
use App\Models\LayananClick;

class FormController extends Controller
{
    /**
     * Menampilkan Layanan Mandiri beserta kategori aktifnya.
     */
    public function show($slug)
    {
        $layanan = Layanan::with('instansi')
            ->where('slug', $slug)
            ->where('jenis', 'form')
            ->where('status', 'aktif')
            ->firstOrFail();

        // Catat klik "Ajukan Layanan"
        LayananClick::recordIfNew($layanan);

        $categories = FormCategory::where('layanan_id', $layanan->id)
            ->where('status', 'aktif')
            ->orderBy('urutan')
            ->get();

        return view('pages.user.form.show', compact(
            'layanan',
            'categories'
        ));
    }

    /**
     * Menampilkan formulir berdasarkan kategori.
     */
    public function form($slug, $categoryId)
    {
        $layanan = Layanan::with('instansi')
            ->where('slug', $slug)
            ->where('jenis', 'form')
            ->where('status', 'aktif')
            ->firstOrFail();

        $category = FormCategory::where('layanan_id', $layanan->id)
            ->where('status', 'aktif')
            ->findOrFail($categoryId);

        return view('pages.user.form.form', compact(
            'layanan',
            'category'
        ));
    }
}