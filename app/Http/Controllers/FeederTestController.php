<?php

namespace App\Http\Controllers;

use App\Services\FeederService;
use Illuminate\Http\Request;

class FeederTestController extends Controller
{
    public function testConnection(FeederService $feeder)
    {
        try {
            // Coba ambil Profil PT sebagai test koneksi
            $result = $feeder->proxy('GetProfilPT');

            return response()->json([
                'status' => 'success',
                'message' => 'Connection to Feeder successful',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function showTestPage()
    {
        return view('feeder.test-view');
    }

    public function submitRequest(Request $request, FeederService $feeder)
    {
        $payload = json_decode($request->payload, true);

        if (!$payload || !isset($payload['act'])) {
            return response()->json([
                'error_code' => 400,
                'error_desc' => 'Payload JSON tidak valid atau field "act" tidak ditemukan.'
            ], 400);
        }

        try {
            // Tambahkan token secara otomatis ke dalam payload
            $payload['token'] = $feeder->getToken();

            // Gunakan Http facade agar semua parameter bebas (key, record, dll) bisa terkirim
            $response = \Illuminate\Support\Facades\Http::post(config('feeder.url'), $payload);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'error_code' => 500,
                'error_desc' => $e->getMessage()
            ], 500);
        }
    }
}
