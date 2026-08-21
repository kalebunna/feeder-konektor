<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BiodataMahasiswaApiController extends Controller
{
    /**
     * Get list of Mahasiswa & Biodata.
     * Wajib menyertakan parameter:
     * - id_prodi (UUID Prodi Neo Feeder)
     * - id_periode (ID Periode Masuk Neo Feeder, contoh: 20231)
     */
    public function index(Request $request): JsonResponse
    {
        $idProdi = $request->input('id_prodi');
        $idPeriode = $request->input('id_periode');

        // Validasi: Kedua parameter wajib ada
        if (empty($idProdi) || empty($idPeriode)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter tidak valid. Parameter "id_prodi" dan "id_periode" wajib diisi.'
            ], 422);
        }

        $mahasiswaList = Mahasiswa::with('biodata')
            ->where('id_prodi', $idProdi)
            ->where('id_periode', $idPeriode)
            ->orderBy('nim', 'asc')
            ->get();

        $formattedData = $mahasiswaList->map(function ($mhs) {
            $bio = $mhs->biodata;
            return [
                'id_mahasiswa' => $mhs->id_mahasiswa,
                'id_registrasi_mahasiswa' => $mhs->id_registrasi_mahasiswa,
                'nim' => $mhs->nim,
                'nama_mahasiswa' => $mhs->nama_mahasiswa,
                'id_prodi' => $mhs->id_prodi,
                'nama_program_studi' => $mhs->nama_program_studi,
                'id_periode' => $mhs->id_periode,
                'nama_periode_masuk' => $mhs->nama_periode_masuk,
                'nama_status_mahasiswa' => $mhs->nama_status_mahasiswa,
                'ipk' => $mhs->ipk,
                'total_sks' => $mhs->total_sks,
                'jenis_kelamin' => $mhs->jenis_kelamin ?? ($bio->jenis_kelamin ?? null),
                'tempat_lahir' => $bio->tempat_lahir ?? null,
                'tanggal_lahir' => $mhs->tanggal_lahir ?? ($bio->tanggal_lahir ?? null),
                'nik' => $bio->nik ?? null,
                'nisn' => $bio->nisn ?? null,
                'agama' => $mhs->nama_agama ?? ($bio->nama_agama ?? null),
                'email' => $bio->email ?? null,
                'handphone' => $bio->handphone ?? null,
                'alamat' => [
                    'jalan' => $bio->jalan ?? null,
                    'dusun' => $bio->dusun ?? null,
                    'rt' => $bio->rt ?? null,
                    'rw' => $bio->rw ?? null,
                    'kelurahan' => $bio->kelurahan ?? null,
                    'kode_pos' => $bio->kode_pos ?? null,
                    'nama_wilayah' => $bio->nama_wilayah ?? null,
                ],
                'orang_tua' => [
                    'nama_ayah' => $bio->nama_ayah ?? null,
                    'nama_ibu_kandung' => $bio->nama_ibu_kandung ?? null,
                    'nama_wali' => $bio->nama_wali ?? null,
                ],
            ];
        });

        return response()->json([
            'status' => 'success',
            'id_prodi' => $idProdi,
            'id_periode' => $idPeriode,
            'total' => $formattedData->count(),
            'data' => $formattedData
        ]);
    }

    /**
     * Get single Biodata Mahasiswa by NIM or id_mahasiswa.
     */
    public function show(string $id): JsonResponse
    {
        $mhs = Mahasiswa::with('biodata')
            ->where('id_mahasiswa', $id)
            ->orWhere('id_registrasi_mahasiswa', $id)
            ->orWhere('nim', $id)
            ->first();

        if (!$mhs) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data mahasiswa tidak ditemukan.'
            ], 404);
        }

        $bio = $mhs->biodata;

        return response()->json([
            'status' => 'success',
            'data' => [
                'id_mahasiswa' => $mhs->id_mahasiswa,
                'id_registrasi_mahasiswa' => $mhs->id_registrasi_mahasiswa,
                'nim' => $mhs->nim,
                'nama_mahasiswa' => $mhs->nama_mahasiswa,
                'id_prodi' => $mhs->id_prodi,
                'nama_program_studi' => $mhs->nama_program_studi,
                'id_periode' => $mhs->id_periode,
                'nama_periode_masuk' => $mhs->nama_periode_masuk,
                'nama_status_mahasiswa' => $mhs->nama_status_mahasiswa,
                'ipk' => $mhs->ipk,
                'total_sks' => $mhs->total_sks,
                'jenis_kelamin' => $mhs->jenis_kelamin ?? ($bio->jenis_kelamin ?? null),
                'tempat_lahir' => $bio->tempat_lahir ?? null,
                'tanggal_lahir' => $mhs->tanggal_lahir ?? ($bio->tanggal_lahir ?? null),
                'nik' => $bio->nik ?? null,
                'nisn' => $bio->nisn ?? null,
                'agama' => $mhs->nama_agama ?? ($bio->nama_agama ?? null),
                'email' => $bio->email ?? null,
                'handphone' => $bio->handphone ?? null,
                'alamat' => [
                    'jalan' => $bio->jalan ?? null,
                    'dusun' => $bio->dusun ?? null,
                    'rt' => $bio->rt ?? null,
                    'rw' => $bio->rw ?? null,
                    'kelurahan' => $bio->kelurahan ?? null,
                    'kode_pos' => $bio->kode_pos ?? null,
                    'nama_wilayah' => $bio->nama_wilayah ?? null,
                ],
                'orang_tua' => [
                    'nama_ayah' => $bio->nama_ayah ?? null,
                    'nama_ibu_kandung' => $bio->nama_ibu_kandung ?? null,
                    'nama_wali' => $bio->nama_wali ?? null,
                ],
            ]
        ]);
    }
}
