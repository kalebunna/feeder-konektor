<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Services\FeederService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\JsonResponse;

class MahasiswaController extends Controller
{
    protected $feeder;

    public function __construct(FeederService $feeder)
    {
        $this->feeder = $feeder;
    }

    public function index(Request $request)
    {
        app()->setLocale('id');
        if ($request->ajax()) {
            $data = Mahasiswa::query()->with(['semester', 'prodi']);

            // Filter by Program Studi (Multiple Select2)
            if ($request->has('prodi_names') && !empty($request->prodi_names)) {
                $data->whereIn('nama_program_studi', $request->prodi_names);
            }

            // Filter by Angkatan / Periode (Multiple Select2)
            if ($request->has('id_periodes') && !empty($request->id_periodes)) {
                $idPeriodes = $request->id_periodes;
                $data->where(function ($q) use ($idPeriodes) {
                    foreach ($idPeriodes as $year) {
                        $q->orWhere('id_periode', 'like', $year . '%');
                    }
                });
            }

            // Filter by Status Mahasiswa (Multiple Select2)
            if ($request->has('status_names') && !empty($request->status_names)) {
                $data->whereIn('nama_status_mahasiswa', $request->status_names);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('tanggal_lahir', function ($row) {
                    return $row->tanggal_lahir ? Carbon::parse($row->tanggal_lahir)->translatedFormat('d F Y') : '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('biodata-mahasiswa.show', $row->id_mahasiswa) . '" class="btn btn-info btn-sm" title="Lihat Biodata"><i class="fas fa-user text-white"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $prodis = Mahasiswa::distinct()->whereNotNull('nama_program_studi')->orderBy('nama_program_studi')->get(['nama_program_studi']);
        $periodes = TahunAjaran::orderBy('id_tahun_ajaran', 'desc')->get(['id_tahun_ajaran', 'nama_tahun_ajaran']);
        $statuses = Mahasiswa::distinct()->whereNotNull('nama_status_mahasiswa')->orderBy('nama_status_mahasiswa')->get(['nama_status_mahasiswa']);
        $prodiList = Prodi::orderBy('nama_program_studi')->get();
        $tahunAjaranList = TahunAjaran::orderBy('id_tahun_ajaran', 'desc')->get();

        return view('admin.mahasiswa.index', compact('prodis', 'periodes', 'statuses', 'prodiList', 'tahunAjaranList'));
    }

    public function sync(Request $request)
    {
        try {
            $id_prodi = $request->id_prodi;
            $id_tahun_ajaran = $request->id_tahun_ajaran;
            $prodisToSync = [];

            if ($id_prodi && $id_prodi !== 'all') {
                $prodisToSync[] = $id_prodi;
            } else {
                $prodisToSync = Prodi::pluck('id_prodi')->toArray();
            }

            // Build Periode/Angkatan filter
            $periodeFilter = '';
            if (!empty($id_tahun_ajaran) && $id_tahun_ajaran !== 'all') {
                $semesters = Semester::where('id_tahun_ajaran', $id_tahun_ajaran)->pluck('id_semester')->toArray();
                if (!empty($semesters)) {
                    $periodeFilter = "id_periode_masuk IN ('" . implode("','", $semesters) . "')";
                } else {
                    $periodeFilter = "id_periode_masuk LIKE '{$id_tahun_ajaran}%'";
                }
            }

            $totalFeeder = 0;
            $totalSaved = 0;

            foreach ($prodisToSync as $prodiId) {
                $filter = "id_prodi='{$prodiId}'";
                if (!empty($periodeFilter)) {
                    $filter .= " AND " . $periodeFilter;
                }

                $response = $this->feeder->proxy('GetDataLengkapMahasiswaProdi', $filter);
                // dd(response()->json($response));
                if (isset($response['error_code']) && $response['error_code'] != 0) {
                    return response()->json(['success' => false, 'message' => $response['error_desc']]);
                }

                if (empty($response['data'])) {
                    continue;
                }

                $data = $response['data'];
                $totalFeeder += count($data);

                foreach ($data as $item) {
                    // Fix date formats
                    $dateFields = ['tanggal_lahir', 'tanggal_keluar', 'tanggal_lahir_ayah', 'tanggal_lahir_ibu', 'tanggal_lahir_wali'];
                    foreach ($dateFields as $field) {
                        if (!empty($item[$field])) {
                            try {
                                $item[$field] = Carbon::createFromFormat('d-m-Y', $item[$field])->format('Y-m-d');
                            } catch (\Exception $e) {
                                try {
                                    $item[$field] = Carbon::parse($item[$field])->format('Y-m-d');
                                } catch (\Exception $e2) {
                                    $item[$field] = null;
                                }
                            }
                        }
                    }

                    // Sanitize identity fields (remove spaces)
                    $identityFields = ['nik', 'nipd', 'nim', 'nisn', 'npwp'];
                    foreach ($identityFields as $field) {
                        if (isset($item[$field])) {
                            $item[$field] = str_replace(' ', '', (string)$item[$field]);
                        }
                    }

                    // Map id_periode_masuk to id_periode if id_periode is not present
                    if (!empty($item['id_periode_masuk']) && empty($item['id_periode'])) {
                        $item['id_periode'] = $item['id_periode_masuk'];
                    }

                    // Define unique keys for updateOrCreate
                    $keys = [];
                    if (!empty($item['id_registrasi_mahasiswa'])) {
                        $keys = ['id_registrasi_mahasiswa' => $item['id_registrasi_mahasiswa']];
                    } else {
                        // Fallback for incomplete data (use id_mahasiswa + id_periode if available)
                        $keys = [
                            'id_mahasiswa' => $item['id_mahasiswa'],
                            'id_periode' => $item['id_periode'] ?? null,
                            'id_registrasi_mahasiswa' => null
                        ];
                    }

                    // Pastikan biodata mahasiswa ada terlebih dahulu agar tidak melanggar foreign key constraint
                    \App\Models\BiodataMahasiswa::updateOrCreate(
                        ['id_mahasiswa' => $item['id_mahasiswa']],
                        $item + ['status_sync' => 'sudah sync']
                    );

                    Mahasiswa::updateOrCreate(
                        $keys,
                        array_merge($item, ['status_sync' => 'sudah sync'])
                    );
                    $totalSaved++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Sinkronisasi selesai. Total data dari Feeder: $totalFeeder, Berhasil disimpan ke database: $totalSaved.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
