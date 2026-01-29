<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\TahunAjaran;
use App\Services\FeederService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

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
            $data = Mahasiswa::query();

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

        return view('admin.mahasiswa.index', compact('prodis', 'periodes', 'statuses'));
    }

    public function sync()
    {
        try {
            $response = $this->feeder->proxy('GetListMahasiswa');

            if (isset($response['error_code']) && $response['error_code'] != 0) {
                return response()->json(['success' => false, 'message' => $response['error_desc']]);
            }

            $data = $response['data'];
            $count = 0;

            foreach ($data as $item) {
                // Fix date formats
                $dateFields = ['tanggal_lahir', 'tanggal_keluar'];
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

                Mahasiswa::updateOrCreate(
                    $keys,
                    array_merge($item, ['status_sync' => 'sudah sync'])
                );
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil sinkronisasi $count data daftar mahasiswa.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
