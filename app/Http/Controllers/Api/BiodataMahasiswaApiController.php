<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BiodataMahasiswa;
use App\Models\Mahasiswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BiodataMahasiswaApiController extends Controller
{
    /**
     * Get list of Biodata Mahasiswa with Neo Feeder IDs and registration details.
     * Query params:
     * - nim: string
     * - id_prodi: string (UUID)
     * - angkatan: string (e.g. 2023)
     * - q: search keyword (nim/nama)
     * - limit: integer
     */
    public function index(Request $request): JsonResponse
    {
        $query = BiodataMahasiswa::with(['mahasiswa' => function ($q) {
            $q->select([
                'id',
                'id_mahasiswa',
                'id_registrasi_mahasiswa',
                'nim',
                'nama_mahasiswa',
                'id_prodi',
                'nama_program_studi',
                'nama_periode_masuk',
                'nama_status_mahasiswa',
                'ipk',
                'total_sks'
            ]);
        }]);

        if ($request->filled('nim')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('nim', $request->nim);
            });
        }

        if ($request->filled('id_prodi')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('id_prodi', $request->id_prodi);
            });
        }

        if ($request->filled('angkatan')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('nama_periode_masuk', 'like', '%' . $request->angkatan . '%');
            });
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_mahasiswa', 'ilike', '%' . $search . '%')
                  ->orWhere('nik', 'ilike', '%' . $search . '%')
                  ->orWhere('nisn', 'ilike', '%' . $search . '%')
                  ->orWhereHas('mahasiswa', function ($mq) use ($search) {
                      $mq->where('nim', 'ilike', '%' . $search . '%');
                  });
            });
        }

        $limit = min(max((int) $request->input('limit', 20), 1), 100);
        $paginated = $query->paginate($limit);

        // Format data to flatten essential feeder fields
        $formattedData = collect($paginated->items())->map(function ($item) {
            return [
                'id_mahasiswa' => $item->id_mahasiswa,
                'id_registrasi_mahasiswa' => $item->mahasiswa->id_registrasi_mahasiswa ?? null,
                'nim' => $item->mahasiswa->nim ?? null,
                'nama_mahasiswa' => $item->nama_mahasiswa,
                'id_prodi' => $item->mahasiswa->id_prodi ?? null,
                'nama_program_studi' => $item->mahasiswa->nama_program_studi ?? null,
                'angkatan' => $item->mahasiswa->nama_periode_masuk ?? null,
                'status_mahasiswa' => $item->mahasiswa->nama_status_mahasiswa ?? null,
                'ipk' => $item->mahasiswa->ipk ?? null,
                'total_sks' => $item->mahasiswa->total_sks ?? null,
                'jenis_kelamin' => $item->jenis_kelamin,
                'tempat_lahir' => $item->tempat_lahir,
                'tanggal_lahir' => $item->tanggal_lahir,
                'nik' => $item->nik,
                'nisn' => $item->nisn,
                'agama' => $item->nama_agama,
                'email' => $item->email,
                'handphone' => $item->handphone,
                'alamat' => [
                    'jalan' => $item->jalan,
                    'dusun' => $item->dusun,
                    'rt' => $item->rt,
                    'rw' => $item->rw,
                    'kelurahan' => $item->kelurahan,
                    'kode_pos' => $item->kode_pos,
                    'wilayah' => $item->nama_wilayah,
                ],
                'orang_tua' => [
                    'nama_ayah' => $item->nama_ayah,
                    'nama_ibu_kandung' => $item->nama_ibu_kandung,
                    'nama_wali' => $item->nama_wali,
                ],
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedData,
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ]
        ]);
    }

    /**
     * Get single Biodata Mahasiswa by NIM or id_mahasiswa.
     */
    public function show(string $id): JsonResponse
    {
        $biodata = BiodataMahasiswa::with('mahasiswa')
            ->where('id_mahasiswa', $id)
            ->orWhereHas('mahasiswa', function ($q) use ($id) {
                $q->where('nim', $id)->orWhere('id_registrasi_mahasiswa', $id);
            })
            ->first();

        if (!$biodata) {
            return response()->json([
                'status' => 'error',
                'message' => 'Biodata mahasiswa tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id_mahasiswa' => $biodata->id_mahasiswa,
                'id_registrasi_mahasiswa' => $biodata->mahasiswa->id_registrasi_mahasiswa ?? null,
                'nim' => $biodata->mahasiswa->nim ?? null,
                'nama_mahasiswa' => $biodata->nama_mahasiswa,
                'id_prodi' => $biodata->mahasiswa->id_prodi ?? null,
                'nama_program_studi' => $biodata->mahasiswa->nama_program_studi ?? null,
                'angkatan' => $biodata->mahasiswa->nama_periode_masuk ?? null,
                'status_mahasiswa' => $biodata->mahasiswa->nama_status_mahasiswa ?? null,
                'ipk' => $biodata->mahasiswa->ipk ?? null,
                'total_sks' => $biodata->mahasiswa->total_sks ?? null,
                'jenis_kelamin' => $biodata->jenis_kelamin,
                'tempat_lahir' => $biodata->tempat_lahir,
                'tanggal_lahir' => $biodata->tanggal_lahir,
                'nik' => $biodata->nik,
                'nisn' => $biodata->nisn,
                'agama' => $biodata->nama_agama,
                'email' => $biodata->email,
                'handphone' => $biodata->handphone,
                'alamat' => [
                    'jalan' => $biodata->jalan,
                    'dusun' => $biodata->dusun,
                    'rt' => $biodata->rt,
                    'rw' => $biodata->rw,
                    'kelurahan' => $biodata->kelurahan,
                    'kode_pos' => $biodata->kode_pos,
                    'wilayah' => $biodata->nama_wilayah,
                ],
                'orang_tua' => [
                    'nama_ayah' => $biodata->nama_ayah,
                    'nama_ibu_kandung' => $biodata->nama_ibu_kandung,
                    'nama_wali' => $biodata->nama_wali,
                ],
                'created_at' => $biodata->created_at,
                'updated_at' => $biodata->updated_at,
            ]
        ]);
    }
}
