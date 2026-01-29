<?php

namespace App\Http\Controllers;

use App\Models\BiodataMahasiswa;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Services\FeederService;
use Carbon\Carbon;

class BiodataMahasiswaController extends Controller
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
            $data = BiodataMahasiswa::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('tanggal_lahir', function ($row) {
                    return $row->tanggal_lahir ? Carbon::parse($row->tanggal_lahir)->translatedFormat('d F Y') : '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('biodata-mahasiswa.show', $row->id_mahasiswa) . '" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Detail</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.biodata_mahasiswa.index');
    }

    public function show($id)
    {
        app()->setLocale('id');
        $mahasiswa = BiodataMahasiswa::with('mahasiswa')->findOrFail($id);
        return view('admin.biodata_mahasiswa.show', compact('mahasiswa'));
    }

    public function sync()
    {
        try {
            $response = $this->feeder->proxy('GetBiodataMahasiswa');

            if (isset($response['error_code']) && $response['error_code'] != 0) {
                return response()->json(['success' => false, 'message' => $response['error_desc']]);
            }

            $data = $response['data'];
            $count = 0;

            foreach ($data as $item) {
                // Fix date formats (NeoFeeder often returns DD-MM-YYYY)
                $dateFields = ['tanggal_lahir', 'tanggal_lahir_ayah', 'tanggal_lahir_ibu', 'tanggal_lahir_wali'];
                foreach ($dateFields as $field) {
                    if (!empty($item[$field])) {
                        try {
                            $item[$field] = Carbon::createFromFormat('d-m-Y', $item[$field])->format('Y-m-d');
                        } catch (\Exception $e) {
                            // If it's already in YYYY-MM-DD or another format, try standard parsing
                            try {
                                $item[$field] = Carbon::parse($item[$field])->format('Y-m-d');
                            } catch (\Exception $e2) {
                                $item[$field] = null;
                            }
                        }
                    }
                }

                // Sanitize identity fields (remove spaces)
                $identityFields = ['nik', 'nik_ayah', 'nik_ibu', 'nisn', 'npwp'];
                foreach ($identityFields as $field) {
                    if (!empty($item[$field])) {
                        $item[$field] = str_replace(' ', '', $item[$field]);
                    }
                }

                BiodataMahasiswa::updateOrCreate(
                    ['id_mahasiswa' => $item['id_mahasiswa']],
                    $item + ['status_sync' => 'sudah sync']
                );
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil sinkronisasi $count data biodata mahasiswa.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
