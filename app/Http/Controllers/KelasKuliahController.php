<?php

namespace App\Http\Controllers;

use App\Services\FeederService;
use App\Models\Prodi;
use App\Models\Semester;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KelasKuliahController extends Controller
{
    protected $feeder;

    public function __construct(FeederService $feeder)
    {
        $this->feeder = $feeder;
    }

    public function index(Request $request)
    {
        $activeSemester = Semester::where('a_periode_aktif', '1')->first();
        $selectedSemesterId = $request->input('id_semester', $activeSemester->id_semester ?? null);

        if ($request->ajax()) {
            try {
                $start = (int)$request->input('start', 0);
                $limit = (int)$request->input('length', 10);
                $search = $request->input('search.value');

                $filters = [];
                if ($selectedSemesterId) {
                    $filters[] = "id_semester = '$selectedSemesterId'";
                }

                if (!empty($search)) {
                    // Simple search filter for nama_mata_kuliah or nama_dosen
                    $searchFilter = "(nama_mata_kuliah LIKE '%$search%' OR nama_dosen LIKE '%$search%' OR nama_kelas_kuliah LIKE '%$search%')";
                    $filters[] = $searchFilter;
                }

                $filter = implode(' AND ', $filters);

                // Get Total Count
                $countResponse = $this->feeder->proxy('GetCountKelasKuliah', $filter);
                $totalRecords = 0;
                if (isset($countResponse['data'])) {
                    $totalRecords = (int)$countResponse['data'];
                }

                // Get Paginated Data
                // Order can also be passed if needed, but for now we follow feeder default
                $response = $this->feeder->proxy('GetListKelasKuliah', $filter, $start, $limit);

                if (isset($response['error_code']) && $response['error_code'] != 0) {
                    return response()->json(['error' => $response['error_desc']], 500);
                }

                $data = $response['data'] ?? [];

                return DataTables::of($data)
                    ->setTotalRecords($totalRecords)
                    ->setFilteredRecords($totalRecords)
                    ->skipPaging() // Critical: we handled paging ourselves
                    ->addIndexColumn()
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        $semesters = Semester::orderBy('id_semester', 'desc')->get();

        return view('admin.kelas-kuliah.index', compact('semesters', 'selectedSemesterId'));
    }

    public function create()
    {
        $prodis = Prodi::orderBy('nama_program_studi')->get();
        $semesters = Semester::where('a_periode_aktif', '1')->orderBy('id_semester', 'desc')->get();

        return view('admin.kelas-kuliah.create', compact('prodis', 'semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_prodi'               => 'required',
            'id_semester'            => 'required',
            'nama_kelas_kuliah'      => 'required',
            'tanggal_mulai_efektif'  => 'required|date_format:Y-m-d',
            'tanggal_akhir_efektif'  => 'required|date_format:Y-m-d',
            'selected_matkul'        => 'required|array',
        ]);

        $successCount = 0;
        $errors = [];

        foreach ($request->selected_matkul as $id_matkul) {
            $data = [
                'id_prodi'              => $request->id_prodi,
                'id_semester'           => $request->id_semester,
                'id_matkul'             => $id_matkul,
                'nama_kelas_kuliah'     => $request->nama_kelas_kuliah,
                'tanggal_mulai_efektif' => $request->tanggal_mulai_efektif,
                'tanggal_akhir_efektif' => $request->tanggal_akhir_efektif,
            ];

            try {
                $response = $this->feeder->post('InsertKelasKuliah', $data);

                if (isset($response['error_code']) && $response['error_code'] == 0) {
                    $successCount++;
                } else {
                    $errors[] = "Matkul ID $id_matkul: " . ($response['error_desc'] ?? 'Unknown error');
                }
            } catch (\Exception $e) {
                $errors[] = "Matkul ID $id_matkul: " . $e->getMessage();
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => $successCount > 0,
                'successCount' => $successCount,
                'errors' => $errors,
                'message' => $successCount > 0 ? "$successCount kelas kuliah berhasil digenerate." : "Gagal melakukan generate kelas kuliah."
            ]);
        }

        if ($successCount > 0) {
            $msg = "$successCount kelas kuliah berhasil digenerate.";
            if (!empty($errors)) {
                $msg .= " Namun terdapat " . count($errors) . " error.";
                return redirect()->route('kelas-kuliah.index')->with('success', $msg)->with('warnings', $errors);
            }
            return redirect()->route('kelas-kuliah.index')->with('success', $msg);
        }

        return redirect()->back()->withErrors($errors)->withInput();
    }

    public function getDetailKurikulum(Request $request)
    {
        $id_prodi = $request->id_prodi;
        $filter = "id_prodi = '$id_prodi'";

        try {
            $response = $this->feeder->proxy('GetListKurikulum', $filter);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error_code' => 500, 'error_desc' => $e->getMessage()], 500);
        }
    }

    public function getMatkulKurikulum(Request $request)
    {
        $id_kurikulum = $request->id_kurikulum;
        $filter = "id_kurikulum = '$id_kurikulum'";

        try {
            $response = $this->feeder->proxy('GetMatkulKurikulum', $filter);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error_code' => 500, 'error_desc' => $e->getMessage()], 500);
        }
    }
}
