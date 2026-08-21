<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SemesterApiController extends Controller
{
    /**
     * Get list of all semesters with Neo Feeder ID.
     */
    public function index(Request $request): JsonResponse
    {
        $data = Semester::orderBy('id_semester', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'total' => $data->count(),
            'data' => $data
        ]);
    }

    /**
     * Get single semester by id_semester.
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
