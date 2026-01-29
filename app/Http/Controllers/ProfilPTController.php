<?php

namespace App\Http\Controllers;

use App\Models\ProfilPT;
use Illuminate\Http\Request;

class ProfilPTController extends Controller
{
    public function index()
    {
        $profil = ProfilPT::first();
        return view('admin.profil-pt.index', compact('profil'));
    }
}
