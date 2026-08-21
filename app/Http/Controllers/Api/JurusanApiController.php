<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JurusanApiController extends Controller
{
    /**
     * Get list of all Program Studi / Jurusan with Neo Feeder IDs.
     */
    public function index(Request $request): JsonResponse
    {
        $data = Prodi::orderBy('nama_program_studi', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'total' => $data->count(),
            'data' => $data
        ]);
    }
}
