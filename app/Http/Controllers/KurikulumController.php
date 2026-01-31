<?php

namespace App\Http\Controllers;

use App\Services\FeederService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KurikulumController extends Controller
{
    protected $feeder;

    public function __construct(FeederService $feeder)
    {
        $this->feeder = $feeder;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $start = (int)$request->input('start', 0);
                $limit = (int)$request->input('length', 10);
                $search = $request->input('search.value');

                $filter = "";
                if (!empty($search)) {
                    $filter = "nama_kurikulum LIKE '%$search%' OR nama_program_studi LIKE '%$search%'";
                }

                // Get Total Count
                $countResponse = $this->feeder->proxy('GetCountKurikulum', $filter);
                $totalRecords = 0;
                if (isset($countResponse['data'])) {
                    $totalRecords = (int)$countResponse['data'];
                }

                // Get Paginated Data
                $response = $this->feeder->proxy('GetDetailKurikulum', $filter, $start, $limit);

                if (isset($response['error_code']) && $response['error_code'] != 0) {
                    return response()->json(['error' => $response['error_desc']], 500);
                }

                $data = $response['data'] ?? [];

                return DataTables::of($data)
                    ->setTotalRecords($totalRecords)
                    ->setFilteredRecords($totalRecords)
                    ->skipPaging()
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        return '<a href="' . route('kurikulum.show', $row['id_kurikulum']) . '" class="btn btn-sm btn-info">
                                    <i class="fas fa-book"></i> Lihat Matkul
                                </a>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return view('admin.kurikulum.index');
    }

    public function show($id)
    {
        try {
            // Get Kurikulum Detail for header
            $kurikulumResponse = $this->feeder->proxy('GetDetailKurikulum', "id_kurikulum = '$id'");
            $kurikulum = $kurikulumResponse['data'][0] ?? null;

            if (!$kurikulum) {
                return redirect()->route('kurikulum.index')->with('error', 'Kurikulum tidak ditemukan.');
            }

            // Get Matkul Kurikulum
            $response = $this->feeder->proxy('GetMatkulKurikulum', "id_kurikulum = '$id'", 0, 0, 'semester ASC');

            if (isset($response['error_code']) && $response['error_code'] != 0) {
                return redirect()->back()->with('error', $response['error_desc']);
            }

            $matkul = $response['data'] ?? [];

            // Grouping by semester and calculating grand totals
            $groupedMatkul = [];
            $totals = [
                'sks_mata_kuliah' => 0,
                'sks_tatap_muka' => 0,
                'sks_praktek' => 0,
                'sks_praktek_lapangan' => 0,
                'sks_simulasi' => 0,
            ];

            foreach ($matkul as $m) {
                $sem = $m['semester'] ?? 'Lainnya';
                $groupedMatkul[$sem][] = $m;

                $totals['sks_mata_kuliah'] += (float)($m['sks_mata_kuliah'] ?? 0);
                $totals['sks_tatap_muka'] += (float)($m['sks_tatap_muka'] ?? 0);
                $totals['sks_praktek'] += (float)($m['sks_praktek'] ?? 0);
                $totals['sks_praktek_lapangan'] += (float)($m['sks_praktek_lapangan'] ?? 0);
                $totals['sks_simulasi'] += (float)($m['sks_simulasi'] ?? 0);
            }

            ksort($groupedMatkul);

            return view('admin.kurikulum.show', compact('kurikulum', 'groupedMatkul', 'totals'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function deleteMatkulKurikulum(Request $request)
    {
        $id_kurikulum = $request->id_kurikulum;
        $id_matkul = $request->id_matkul;

        $record = [
            'id_kurikulum' => $id_kurikulum,
            'id_matkul' => $id_matkul
        ];

        try {
            $response = $this->feeder->delete('DeleteMatkulKurikulum', $record);

            if (isset($response['error_code']) && $response['error_code'] != 0) {
                return response()->json(['success' => false, 'message' => $response['error_desc']], 400);
            }

            return response()->json(['success' => true, 'message' => 'Mata kuliah berhasil dihapus dari kurikulum.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getMatakuliahList(Request $request)
    {
        $search = $request->search;
        $filter = "";
        if (!empty($search)) {
            $filter = "nama_mata_kuliah LIKE '%$search%' OR kode_mata_kuliah LIKE '%$search%'";
        }

        try {
            // Limited to 50 for selection performance
            $response = $this->feeder->proxy('GetListMataKuliah', $filter, 0, 50);
            return response()->json($response['data'] ?? []);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeMatkulKurikulumBulk(Request $request)
    {
        $request->validate([
            'id_kurikulum' => 'required',
            'semester' => 'required|numeric',
            'matkul' => 'required|array',
            'matkul.*.id_matkul' => 'required'
        ]);

        $id_kurikulum = $request->id_kurikulum;
        $semester = $request->semester;
        $successCount = 0;
        $errors = [];

        foreach ($request->matkul as $mk) {
            $record = [
                'id_kurikulum'         => $id_kurikulum,
                'id_matkul'            => $mk['id_matkul'],
                'semester'             => (int)$semester,
                'sks_mata_kuliah'      => (float)($mk['sks_mata_kuliah'] ?? 0),
                'sks_tatap_muka'       => (float)($mk['sks_tatap_muka'] ?? 0),
                'sks_praktek'          => (float)($mk['sks_praktek'] ?? 0),
                'sks_praktek_lapangan' => (float)($mk['sks_praktek_lapangan'] ?? 0),
                'sks_simulasi'         => (float)($mk['sks_simulasi'] ?? 0),
                'apakah_wajib'         => isset($mk['apakah_wajib']) ? 1 : 0,
            ];

            try {
                $response = $this->feeder->post('InsertMatkulKurikulum', $record);
                if (isset($response['error_code']) && $response['error_code'] == 0) {
                    $successCount++;
                } else {
                    $errors[] = "Matkul ID {$mk['id_matkul']}: " . ($response['error_desc'] ?? 'Unknown error');
                }
            } catch (\Exception $e) {
                $errors[] = "Matkul ID {$mk['id_matkul']}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => $successCount > 0,
            'successCount' => $successCount,
            'errors' => $errors,
            'message' => $successCount > 0 ? "$successCount mata kuliah berhasil ditambahkan ke kurikulum." : "Gagal menambahkan mata kuliah."
        ]);
    }

    public function getKurikulumList(Request $request)
    {
        $search = $request->search;
        $filter = "";
        if (!empty($search)) {
            $filter = "nama_kurikulum LIKE '%$search%' OR nama_program_studi LIKE '%$search%'";
        }

        try {
            $response = $this->feeder->proxy('GetListKurikulum', $filter, 0, 50);
            return response()->json($response['data'] ?? []);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function copyMatkulKurikulum(Request $request)
    {
        $request->validate([
            'id_kurikulum_target' => 'required',
            'id_kurikulum_source' => 'required'
        ]);

        $id_target = $request->id_kurikulum_target;
        $id_source = $request->id_kurikulum_source;

        try {
            // 1. Get courses from source
            $sourceResponse = $this->feeder->proxy('GetMatkulKurikulum', "id_kurikulum = '$id_source'");

            if (isset($sourceResponse['error_code']) && $sourceResponse['error_code'] != 0) {
                return response()->json(['success' => false, 'message' => 'Gagal mengambil data sumber: ' . $sourceResponse['error_desc']], 400);
            }

            $courses = $sourceResponse['data'] ?? [];

            if (empty($courses)) {
                return response()->json(['success' => false, 'message' => 'Kurikulum sumber tidak memiliki mata kuliah.'], 400);
            }

            $successCount = 0;
            $errors = [];

            // 2. Loop and Insert into target
            foreach ($courses as $mk) {
                $record = [
                    'id_kurikulum'         => $id_target,
                    'id_matkul'            => $mk['id_matkul'],
                    'semester'             => (int)($mk['semester'] ?? 1),
                    'sks_mata_kuliah'      => (float)($mk['sks_mata_kuliah'] ?? 0),
                    'sks_tatap_muka'       => (float)($mk['sks_tatap_muka'] ?? 0),
                    'sks_praktek'          => (float)($mk['sks_praktek'] ?? 0),
                    'sks_praktek_lapangan' => (float)($mk['sks_praktek_lapangan'] ?? 0),
                    'sks_simulasi'         => (float)($mk['sks_simulasi'] ?? 0),
                    'apakah_wajib'         => (int)($mk['apakah_wajib'] ?? 1),
                ];

                $response = $this->feeder->post('InsertMatkulKurikulum', $record);
                if (isset($response['error_code']) && $response['error_code'] == 0) {
                    $successCount++;
                } else {
                    $errors[] = "Matkul {$mk['nama_mata_kuliah']}: " . ($response['error_desc'] ?? 'Unknown error');
                }
            }

            return response()->json([
                'success' => $successCount > 0,
                'successCount' => $successCount,
                'errors' => $errors,
                'message' => "Berhasil menyalin $successCount mata kuliah ke kurikulum target."
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
