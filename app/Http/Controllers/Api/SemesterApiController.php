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
}
