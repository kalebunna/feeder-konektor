<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use App\Services\FeederService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class TahunAjaranController extends Controller
{
    protected $feeder;

    public function __construct(FeederService $feeder)
    {
        $this->feeder = $feeder;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TahunAjaran::query()->orderBy('id_tahun_ajaran', 'desc');
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

        $activeYear = TahunAjaran::where('a_periode_aktif', '1')->first();

        return view('admin.tahun_ajaran.index', compact('activeYear'));
    }

    public function sync(Request $request)
    {
        $request->validate([
            'tahun_mulai' => 'required|numeric',
            'tahun_sampai' => 'required|numeric|gte:tahun_mulai',
        ]);

        $filter = "id_tahun_ajaran >= '{$request->tahun_mulai}' AND id_tahun_ajaran <= '{$request->tahun_sampai}'";

        try {
            $response = $this->feeder->proxy('GetTahunAjaran', $filter);

            if (isset($response['error_code']) && $response['error_code'] != 0) {
                return response()->json(['success' => false, 'message' => $response['error_desc']]);
            }

            $data = $response['data'];
            $count = 0;

            foreach ($data as $item) {
                TahunAjaran::updateOrCreate(
                    ['id_tahun_ajaran' => $item['id_tahun_ajaran']],
                    [
                        'nama_tahun_ajaran' => $item['nama_tahun_ajaran'],
                        'a_periode_aktif' => $item['a_periode_aktif'],
                        'tanggal_mulai' => !empty($item['tanggal_mulai']) ? Carbon::parse($item['tanggal_mulai'])->format('Y-m-d') : null,
                        'tanggal_selesai' => !empty($item['tanggal_selesai']) ? Carbon::parse($item['tanggal_selesai'])->format('Y-m-d') : null,
                    ]
                );
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil sinkronisasi $count data Tahun Ajaran.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function syncActiveStatus()
    {
        try {
            // Get current active from feeder (1 = active)
            $response = $this->feeder->proxy('GetTahunAjaran', "a_periode_aktif = '1'");

            if (isset($response['error_code']) && $response['error_code'] != 0) {
                return response()->json(['success' => false, 'message' => $response['error_desc']]);
            }

            if (empty($response['data'])) {
                return response()->json(['success' => false, 'message' => 'Tidak ada periode aktif ditemukan di Feeder.']);
            }

            $feederActive = $response['data'][0];
            $dbActive = TahunAjaran::where('a_periode_aktif', '1')->first();

            if (!$dbActive || $dbActive->id_tahun_ajaran != $feederActive['id_tahun_ajaran']) {
                // Update all to inactive first (0 = inactive)
                TahunAjaran::where('a_periode_aktif', '1')->update(['a_periode_aktif' => '0']);

                // Update or Create the new active one (1 = active)
                TahunAjaran::updateOrCreate(
                    ['id_tahun_ajaran' => $feederActive['id_tahun_ajaran']],
                    [
                        'nama_tahun_ajaran' => $feederActive['nama_tahun_ajaran'],
                        'a_periode_aktif' => '1',
                        'tanggal_mulai' => !empty($feederActive['tanggal_mulai']) ? Carbon::parse($feederActive['tanggal_mulai'])->format('Y-m-d') : null,
                        'tanggal_selesai' => !empty($feederActive['tanggal_selesai']) ? Carbon::parse($feederActive['tanggal_selesai'])->format('Y-m-d') : null,
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => "Status periode aktif diperbarui ke {$feederActive['nama_tahun_ajaran']}.",
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status periode aktif di database sudah sesuai dengan Feeder.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
