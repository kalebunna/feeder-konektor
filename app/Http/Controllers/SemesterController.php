<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Services\FeederService;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class SemesterController extends Controller
{
    protected $feeder;

    public function __construct(FeederService $feeder)
    {
        $this->feeder = $feeder;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Semester::with('tahunAjaran')->orderBy('id_semester', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('a_periode_aktif', function ($row) {
                    if ($row->a_periode_aktif == '1') {
                        return '<span class="badge bg-success">Aktif</span>';
                    }
                    return '<span class="badge bg-secondary">Tidak Aktif</span>';
                })
                ->editColumn('tanggal_mulai', function ($row) {
                    return $row->tanggal_mulai ? Carbon::parse($row->tanggal_mulai)->format('d-m-Y') : '-';
                })
                ->editColumn('tanggal_selesai', function ($row) {
                    return $row->tanggal_selesai ? Carbon::parse($row->tanggal_selesai)->format('d-m-Y') : '-';
                })
                ->rawColumns(['a_periode_aktif'])
                ->make(true);
        }

        $activeSemester = Semester::where('a_periode_aktif', '1')->first();

        return view('admin.semester.index', compact('activeSemester'));
    }

    public function sync()
    {
        // Get all id_tahun_ajaran from database
        $tahunAjarans = TahunAjaran::pluck('id_tahun_ajaran')->toArray();

        if (empty($tahunAjarans)) {
            return response()->json(['success' => false, 'message' => 'Silahkan sinkronisasi Tahun Ajaran terlebih dahulu.']);
        }

        $count = 0;
        try {
            foreach ($tahunAjarans as $id_tahun_ajaran) {
                $filter = "id_tahun_ajaran = '{$id_tahun_ajaran}'";
                $response = $this->feeder->proxy('GetSemester', $filter);

                if (isset($response['error_code']) && $response['error_code'] != 0) {
                    continue; // Skip if error for specific year
                }

                if (!empty($response['data'])) {
                    foreach ($response['data'] as $item) {
                        Semester::updateOrCreate(
                            ['id_semester' => $item['id_semester']],
                            [
                                'id_tahun_ajaran' => $item['id_tahun_ajaran'],
                                'nama_semester' => $item['nama_semester'],
                                'semester' => $item['semester'],
                                'a_periode_aktif' => $item['a_periode_aktif'],
                                'tanggal_mulai' => !empty($item['tanggal_mulai']) ? Carbon::parse($item['tanggal_mulai'])->format('Y-m-d') : null,
                                'tanggal_selesai' => !empty($item['tanggal_selesai']) ? Carbon::parse($item['tanggal_selesai'])->format('Y-m-d') : null,
                            ]
                        );
                        $count++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil sinkronisasi $count data Semester.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function syncActiveStatus()
    {
        try {
            // Get current active from feeder (1 = active)
            $response = $this->feeder->proxy('GetSemester', "a_periode_aktif = '1'");

            if (isset($response['error_code']) && $response['error_code'] != 0) {
                return response()->json(['success' => false, 'message' => $response['error_desc']]);
            }

            if (empty($response['data'])) {
                return response()->json(['success' => false, 'message' => 'Tidak ada semester aktif ditemukan di Feeder.']);
            }

            $feederActive = $response['data'][0];

            // Check if tahun ajaran exists in DB
            $checkTahun = TahunAjaran::where('id_tahun_ajaran', $feederActive['id_tahun_ajaran'])->exists();
            if (!$checkTahun) {
                return response()->json(['success' => false, 'message' => "Tahun Ajaran {$feederActive['id_tahun_ajaran']} belum ada di database. Silahkan sinkronisasi Tahun Ajaran terlebih dahulu."]);
            }

            $dbActive = Semester::where('a_periode_aktif', '1')->first();

            if (!$dbActive || $dbActive->id_semester != $feederActive['id_semester']) {
                // Update all to inactive first
                Semester::where('a_periode_aktif', '1')->update(['a_periode_aktif' => '0']);

                // Update or Create the new active one
                Semester::updateOrCreate(
                    ['id_semester' => $feederActive['id_semester']],
                    [
                        'id_tahun_ajaran' => $feederActive['id_tahun_ajaran'],
                        'nama_semester' => $feederActive['nama_semester'],
                        'semester' => $feederActive['semester'],
                        'a_periode_aktif' => '1',
                        'tanggal_mulai' => !empty($feederActive['tanggal_mulai']) ? Carbon::parse($feederActive['tanggal_mulai'])->format('Y-m-d') : null,
                        'tanggal_selesai' => !empty($feederActive['tanggal_selesai']) ? Carbon::parse($feederActive['tanggal_selesai'])->format('Y-m-d') : null,
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => "Status semester aktif diperbarui ke {$feederActive['nama_semester']}.",
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status semester aktif di database sudah sesuai dengan Feeder.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
