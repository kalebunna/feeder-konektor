<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index()
    {
        $prodis = Prodi::orderBy('nama_program_studi')->get();
        return view('admin.prodi.index', compact('prodis'));
    }
}
