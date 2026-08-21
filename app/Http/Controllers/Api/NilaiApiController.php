<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FeederService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NilaiApiController extends Controller
{
    protected $feeder;

    public function __construct(FeederService $feeder)
    {
        $this->feeder = $feeder;
    }

    /**
     * Get list of Nilai Perkuliahan Mahasiswa per Prodi & Semester.
     * Wajib menyertakan parameter:
     * - id_prodi (UUID Program Studi dari Neo Feeder)
     * - id_semester (ID Semester Neo Feeder, contoh: 20241 untuk Ganjil, 20242 untuk Genap)
     */
    public function index(Request $request): JsonResponse
    {
        $idProdi = $request->input('id_prodi');
        $idSemester = $request->input('id_semester');

        // Validasi: Kedua parameter wajib ada
        if (empty($idProdi) || empty($idSemester)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter tidak valid. Parameter "id_prodi" dan "id_semester" wajib diisi.'
            ], 422);
        }

        try {
            $filter = "id_prodi = '{$idProdi}' AND id_semester = '{$idSemester}'";

            // Ambil semua data nilai dari Neo Feeder tanpa batas limit (0 = all)
            $response = $this->feeder->proxy('GetDetailNilaiPerkuliahanKelas', $filter, 0, 0);

            if (isset($response['error_code']) && $response['error_code'] != 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => $response['error_desc'] ?? 'Gagal mengambil data nilai dari Feeder.'
                ], 500);
            }

            $rawNilai = $response['data'] ?? [];

            // Sort berdasarkan NIM agar rapi
            $formattedNilai = collect($rawNilai)->sortBy('nim')->values()->map(function ($item) {
                return [
                    'id_prodi' => $item['id_prodi'] ?? null,
                    'nama_program_studi' => $item['nama_program_studi'] ?? null,
                    'id_semester' => $item['id_semester'] ?? null,
                    'nama_semester' => $item['nama_semester'] ?? null,
                    'id_matkul' => $item['id_matkul'] ?? null,
                    'kode_mata_kuliah' => $item['kode_mata_kuliah'] ?? null,
                    'nama_mata_kuliah' => $item['nama_mata_kuliah'] ?? null,
                    'sks_mata_kuliah' => (float)($item['sks_mata_kuliah'] ?? 0),
                    'id_kelas_kuliah' => $item['id_kelas_kuliah'] ?? null,
                    'nama_kelas_kuliah' => $item['nama_kelas_kuliah'] ?? null,
                    'id_registrasi_mahasiswa' => $item['id_registrasi_mahasiswa'] ?? null,
                    'id_mahasiswa' => $item['id_mahasiswa'] ?? null,
                    'nim' => $item['nim'] ?? null,
                    'nama_mahasiswa' => $item['nama_mahasiswa'] ?? null,
                    'angkatan' => $item['angkatan'] ?? null,
                    'nilai_angka' => $item['nilai_angka'] !== null ? (float)$item['nilai_angka'] : null,
                    'nilai_huruf' => $item['nilai_huruf'] ?? null,
                    'nilai_indeks' => $item['nilai_indeks'] !== null ? (float)$item['nilai_indeks'] : null,
                ];
            });

            return response()->json([
                'status' => 'success',
                'id_prodi' => $idProdi,
                'id_semester' => $idSemester,
                'total' => $formattedNilai->count(),
                'data' => $formattedNilai
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}
