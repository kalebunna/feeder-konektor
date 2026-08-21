<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiTokenController extends Controller
{
    /**
     * Display list of API tokens.
     */
    public function index(Request $request): View
    {
        $tokens = $request->user()->tokens()->orderBy('created_at', 'desc')->get();
        return view('admin.api_tokens.index', compact('tokens'));
    }

    /**
     * Create a new API token.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'abilities' => 'nullable|array',
            'expires_in' => 'nullable|integer', // in days
        ]);

        $abilities = $request->input('abilities', ['*']);
        if (empty($abilities) || in_array('*', $abilities)) {
            $abilities = ['*'];
        }

        $expiresAt = null;
        if ($request->filled('expires_in') && (int)$request->expires_in > 0) {
            $expiresAt = now()->addDays((int)$request->expires_in);
        }

        $token = $request->user()->createToken($request->name, $abilities, $expiresAt);

        return response()->json([
            'success' => true,
            'message' => 'Token API berhasil dibuat!',
            'token' => $token->plainTextToken,
            'token_name' => $request->name
        ]);
    }

    /**
     * Revoke / delete an API token.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $deleted = $request->user()->tokens()->where('id', $id)->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Token API berhasil dicabut (dihapus).'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Token tidak ditemukan atau gagal dihapus.'
        ], 404);
    }
}
