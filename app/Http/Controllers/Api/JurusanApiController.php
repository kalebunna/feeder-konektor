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

    /**
     * Get single Program Studi by ID (UUID / id_prodi).
     */
    public function show(string $id): JsonResponse
    {
        $prodi = Prodi::where('id', $id)
            ->orWhere('id_prodi', $id)
            ->first();

        if (!$prodi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Program Studi / Jurusan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $prodi
        ]);
    }
}
