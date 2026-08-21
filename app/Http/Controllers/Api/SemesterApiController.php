<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SemesterApiController extends Controller
{
    /**
     * Get list of semesters.
     * Query params:
     * - aktif: 1 (only active semester)
     * - tahun_ajaran: id_tahun_ajaran
     * - limit: integer (default 50)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Semester::query();

        if ($request->has('aktif') && $request->aktif == '1') {
            $query->where('a_periode_aktif', 1);
        }

        if ($request->filled('id_tahun_ajaran')) {
            $query->where('id_tahun_ajaran', $request->id_tahun_ajaran);
        }

        if ($request->filled('q')) {
            $query->where('nama_semester', 'ilike', '%' . $request->q . '%')
                  ->orWhere('id_semester', 'ilike', '%' . $request->q . '%');
        }

        $query->orderBy('id_semester', 'desc');

        if ($request->boolean('all')) {
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
     * Get single semester by ID.
     */
    public function show(string $id): JsonResponse
    {
        $semester = Semester::where('id_semester', $id)->first();

        if (!$semester) {
            return response()->json([
                'status' => 'error',
                'message' => 'Semester tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $semester
        ]);
    }
}
