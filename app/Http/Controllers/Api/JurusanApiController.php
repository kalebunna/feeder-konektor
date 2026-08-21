<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JurusanApiController extends Controller
{
    /**
     * Get list of Program Studi / Jurusan with Neo Feeder IDs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Prodi::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_program_studi', 'ilike', '%' . $request->q . '%')
                  ->orWhere('kode_program_studi', 'ilike', '%' . $request->q . '%')
                  ->orWhere('id_prodi', 'ilike', '%' . $request->q . '%');
            });
        }

        $query->orderBy('nama_program_studi', 'asc');

        if ($request->boolean('all', true)) {
            $data = $query->get();
            return response()->json([
                'status' => 'success',
                'total' => $data->count(),
                'data' => $data
            ]);
        }

        $limit = min(max((int) $request->input('limit', 20), 1), 100);
        $paginated = $query->paginate($limit);

        return response()->json([
            'status' => 'success',
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ]
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
