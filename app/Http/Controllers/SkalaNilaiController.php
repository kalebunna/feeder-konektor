<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SkalaNilai;

class SkalaNilaiController extends Controller
{
    public function index()
    {
        $skalaNilais = SkalaNilai::orderBy('nilai_indeks', 'desc')->get();
        return view('admin.skala_nilai.index', compact('skalaNilais'));
    }
}
