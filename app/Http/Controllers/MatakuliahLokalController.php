<?php

namespace App\Http\Controllers;

use App\Models\MatakuliahLokal;
use App\Models\Prodi;
use App\Services\FeederService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatakuliahLokalController extends Controller
{
    protected $feeder;

    public function __construct(FeederService $feeder)
    {
        $this->feeder = $feeder;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $matakuliah = MatakuliahLokal::all();
        $prodis = Prodi::all();
        return view('admin.matakuliah-lokal.index', compact('matakuliah', 'prodis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mata_kuliah' => 'required|string',
            'nama_mata_kuliah' => 'required|string',
            'sks_mata_kuliah' => 'required|numeric',
            'id_prodi' => 'required|uuid',
            'nama_program_studi' => 'required|string',
            'sks_tatap_muka' => 'required|numeric',
            'sks_praktek' => 'required|numeric',
            'sks_praktek_lapangan' => 'required|numeric',
            'sks_simulasi' => 'required|numeric',
            'status' => 'nullable|in:sudah sync,belum sync',
        ]);

        $matakuliah = MatakuliahLokal::create($validated);
        return response()->json($matakuliah, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $matakuliah = MatakuliahLokal::findOrFail($id);
        return response()->json($matakuliah);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $matakuliah = MatakuliahLokal::findOrFail($id);

        $validated = $request->validate([
            'kode_mata_kuliah' => 'required|string',
            'nama_mata_kuliah' => 'required|string',
            'sks_mata_kuliah' => 'required|numeric',
            'sks_tatap_muka' => 'required|numeric',
            'sks_praktek' => 'required|numeric',
            'sks_praktek_lapangan' => 'required|numeric',
            'sks_simulasi' => 'required|numeric',
        ]);

        $matakuliah->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data mata kuliah berhasil diperbarui.',
            'data' => $matakuliah
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $matakuliah = MatakuliahLokal::findOrFail($id);
            $matakuliah->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data mata kuliah berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sync(Request $request, $id)
    {
        $matakuliah = MatakuliahLokal::findOrFail($id);

        $request->validate([
            'id_jenis_mata_kuliah' => 'nullable|string',
            'id_kelompok_mata_kuliah' => 'nullable|string',
        ]);

        try {
            $record = [
                'kode_mata_kuliah' => $matakuliah->kode_mata_kuliah,
                'nama_mata_kuliah' => $matakuliah->nama_mata_kuliah,
                'id_prodi' => $matakuliah->id_prodi,
                'id_jenis_mata_kuliah' => $request->id_jenis_mata_kuliah,
                'id_kelompok_mata_kuliah' => $request->id_kelompok_mata_kuliah,
                'sks_mata_kuliah' => $matakuliah->sks_mata_kuliah,
                'sks_tatap_muka' => $matakuliah->sks_tatap_muka,
                'sks_praktek' => $matakuliah->sks_praktek,
                'sks_praktek_lapangan' => $matakuliah->sks_praktek_lapangan,
                'sks_simulasi' => $matakuliah->sks_simulasi,
            ];

            $response = $this->feeder->post('InsertMataKuliah', $record);

            if (isset($response['error_code']) && $response['error_code'] != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal Sinkron: ' . $response['error_desc']
                ], 400);
            }

            // Update status
            $matakuliah->update(['status' => 'sudah sync']);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Sinkron ke Feeder.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal Sinkron: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkSync(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:matakuliah_lokal,id',
            'id_jenis_mata_kuliah' => 'nullable|string',
            'id_kelompok_mata_kuliah' => 'nullable|string',
        ]);

        $ids = $request->ids;
        $successCount = 0;
        $errors = [];

        foreach ($ids as $id) {
            $matakuliah = MatakuliahLokal::find($id);

            try {
                $record = [
                    'kode_mata_kuliah' => $matakuliah->kode_mata_kuliah,
                    'nama_mata_kuliah' => $matakuliah->nama_mata_kuliah,
                    'id_prodi' => $matakuliah->id_prodi,
                    'id_jenis_mata_kuliah' => $request->id_jenis_mata_kuliah,
                    'id_kelompok_mata_kuliah' => $request->id_kelompok_mata_kuliah,
                    'sks_mata_kuliah' => $matakuliah->sks_mata_kuliah,
                    'sks_tatap_muka' => $matakuliah->sks_tatap_muka,
                    'sks_praktek' => $matakuliah->sks_praktek,
                    'sks_praktek_lapangan' => $matakuliah->sks_praktek_lapangan,
                    'sks_simulasi' => $matakuliah->sks_simulasi,
                ];

                $response = $this->feeder->post('InsertMataKuliah', $record);

                if (isset($response['error_code']) && $response['error_code'] != 0) {
                    $errors[] = "MK {$matakuliah->kode_mata_kuliah}: " . $response['error_desc'];
                    continue;
                }

                $matakuliah->update(['status' => 'sudah sync']);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "MK {$matakuliah->kode_mata_kuliah}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil sinkronisasi $successCount data." . (count($errors) > 0 ? " Terdapat beberapa error." : ""),
            'errors' => $errors
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'id_prodi' => 'required|exists:prodis,id_prodi',
            'file_csv' => 'required|file|mimes:csv,txt',
        ]);

        $prodi = Prodi::where('id_prodi', $request->id_prodi)->first();
        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), 'r');

        // Skip header
        fgetcsv($handle, 1000, ";");

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                // Skip empty lines or lines with missing data
                if (empty($data[1])) continue;

                $sks_tatap_muka = (float)str_replace(',', '.', $data[2] ?? 0);
                $sks_praktek_lapangan = (float)str_replace(',', '.', $data[3] ?? 0);
                $sks_praktek = (float)str_replace(',', '.', $data[4] ?? 0);
                $sks_simulasi = (float)str_replace(',', '.', $data[5] ?? 0);

                $total_sks = $sks_tatap_muka + $sks_praktek_lapangan + $sks_praktek + $sks_simulasi;

                MatakuliahLokal::create([
                    'kode_mata_kuliah' => $data[0] ?: 'TBA',
                    'nama_mata_kuliah' => $data[1],
                    'sks_mata_kuliah' => $total_sks,
                    'id_prodi' => $prodi->id_prodi,
                    'nama_program_studi' => $prodi->nama_program_studi,
                    'sks_tatap_muka' => $sks_tatap_muka,
                    'sks_praktek' => $sks_praktek,
                    'sks_praktek_lapangan' => $sks_praktek_lapangan,
                    'sks_simulasi' => $sks_simulasi,
                    'status' => 'belum sync',
                ]);
            }
            DB::commit();
            fclose($handle);
            return redirect()->back()->with('success', 'Data mata kuliah berhasil diimpor.');
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }
}
