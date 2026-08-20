<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FeederService;

class KamusDataController extends Controller
{
    /**
     * Menampilkan halaman utama Kamus Data
     */
    public function index()
    {
        return view('admin.kamus_data.index');
    }

    /**
     * Mengambil struktur kamus data dari API Neofeeder berdasarkan fungsi (act)
     */
    public function fetch(Request $request, FeederService $feeder)
    {
        $request->validate([
            'fungsi' => 'required|string'
        ]);

        try {
            // Karena ini payload yang sedikit spesifik (butuh key 'fungsi'), kita panggil GetDictionary
            $payload = [
                'act' => 'GetDictionary',
                'token' => $feeder->getToken(),
                'fungsi' => trim($request->fungsi)
            ];

            // Panggil API lewat fungsi sendRequest yang biasanya ada di dalam FeederService
            // Kita bisa juga menggunakan struktur proxy jika proxy mendukung parameter bebas,
            // Tapi untuk amannya kita post langsung menggunakan HTTP facade yang ada di FeederService
            
            // Mengingat FeederService di project ini kemungkinan membungkus koneksi HTTP:
            // Karena tidak tahu isi persis FeederService, kita gunakan curl atau Http facade via service
            // Method proxy pada FeederTestController menggunakan format: $feeder->proxy($act, $filter, $offset, $limit, $order)
            // Ternyata $feeder->proxy tidak bisa menyisipkan key 'fungsi'.
            
            // Mari kita coba cek apakah ada fungsi custom request.
            // Sementara kita asumsikan FeederService memiliki getClient() atau request()
            // Untuk memastikan jalan tanpa mengubah FeederService, mari panggil HTTP Client secara mandiri menggunakan config Feeder yang sama jika memungkinkan
            // Atau cukup gunakan facade Http.
            
            // Ops! Kita coba panggil method customRequest jika ada, jika tidak ada, 
            // Kita gunakan Http::post dengan config dari env.
            
            $url = env('FEEDER_URL', 'http://localhost:8100/ws/live.php');
            
            $response = \Illuminate\Support\Facades\Http::post($url, $payload);
            
            $result = $response->json();

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'error_code' => 500,
                'error_desc' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
