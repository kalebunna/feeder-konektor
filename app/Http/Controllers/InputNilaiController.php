<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FeederService;
use App\Models\Semester;
use App\Models\SkalaNilai;

class InputNilaiController extends Controller
{
    protected $feeder;

    public function __construct(FeederService $feeder)
    {
        $this->feeder = $feeder;
    }

    /**
     * Menampilkan daftar kelas kuliah semester berjalan
     */
    public function index(Request $request)
    {
        $activeSemester = Semester::where('a_periode_aktif', '1')->first();
        if (!$activeSemester) {
            return redirect()->back()->with('error', 'Tidak ada semester aktif. Silakan seting semester aktif terlebih dahulu.');
        }

        $id_semester = $activeSemester->id_semester;
        $filter = "id_semester = '$id_semester'";

        // Ambil kelas langsung dari Feeder
        try {
            $response = $this->feeder->proxy('GetListKelasKuliah', $filter, 0, 500); // Ambil maks 500 kelas
            $kelas = $response['data'] ?? [];
        } catch (\Exception $e) {
            $kelas = [];
            session()->flash('error', 'Gagal mengambil data kelas dari Feeder: ' . $e->getMessage());
        }

        return view('admin.input_nilai.index', compact('kelas', 'activeSemester'));
    }

    /**
     * Halaman detail satu kelas (KRS & Input Nilai)
     */
    public function show($id_kelas)
    {
        // Ambil info detail kelas
        $filterKelas = "id_kelas_kuliah = '$id_kelas'";
        $detailKelas = null;
        try {
            $respKelas = $this->feeder->proxy('GetListKelasKuliah', $filterKelas);
            $detailKelas = $respKelas['data'][0] ?? null;
        } catch (\Exception $e) { }

        if (!$detailKelas) {
            return redirect()->route('input-nilai.index')->with('error', 'Kelas tidak ditemukan di Feeder.');
        }

        // Ambil Skala Nilai untuk keperluan auto-calculate di Javascript frontend
        // Kita parsing array json agar gampang diakses via js
        $id_prodi = $detailKelas['id_prodi'] ?? null;
        $skalaNilai = [];
        if ($id_prodi) {
            $skalaResponse = $this->feeder->proxy('GetListSkalaNilaiProdi', "id_prodi = '$id_prodi'", 0, 100);
            $skalaNilai = $skalaResponse['data'] ?? [];
        }

        // Ambil daftar Tahun Masuk/Angkatan unik dari database lokal
        $angkatanList = \App\Models\Mahasiswa::select('nama_periode_masuk')
            ->whereNotNull('nama_periode_masuk')
            ->where('nama_periode_masuk', '!=', '')
            ->distinct()
            ->orderBy('nama_periode_masuk', 'desc')
            ->pluck('nama_periode_masuk');

        return view('admin.input_nilai.show', compact('id_kelas', 'detailKelas', 'skalaNilai', 'angkatanList'));
    }

    /**
     * AJAX: Ambil daftar peserta kelas beserta nilainya
     */
    public function getPeserta($id_kelas)
    {
        // Untuk nilai, kita gunakan GetDetailNilaiPerkuliahanKelas karena fungsi ini
        // akan mengembalikan detail mahasiswa berserta nilainya sekaligus (nilai_angka, nilai_huruf, nilai_indeks).
        // Fungsi GetPesertaKelasKuliah hanya mengembalikan data peserta tanpa nilai.
        
        $filter = "id_kelas_kuliah = '$id_kelas'";
        try {
            $response = $this->feeder->proxy('GetDetailNilaiPerkuliahanKelas', $filter, 0, 500);
            
            $data = $response['data'] ?? [];
            if (!empty($data)) {
                $data = collect($data)->sortBy('nim')->values()->all();
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX: Cari Mahasiswa (untuk KRS)
     */
    public function cariMahasiswa(Request $request)
    {
        $id_periode = $request->input('id_periode'); // angkatan
        $nama = $request->input('nama'); // pencarian nama spesifik
        $id_prodi = $request->input('id_prodi');

        try {
            $query = \App\Models\Mahasiswa::query();

            if ($id_prodi) {
                $query->where('id_prodi', $id_prodi);
            }

            if ($id_periode) {
                $query->where(function($q) use ($id_periode) {
                    $q->where('nama_periode_masuk', 'LIKE', "%$id_periode%")
                      ->orWhere('nim', 'LIKE', "$id_periode%");
                });
            }

            if ($nama) {
                $query->where('nama_mahasiswa', 'LIKE', "%$nama%");
            }

            $mahasiswas = $query->orderBy('nim')->limit(200)->get();

            $data = $mahasiswas->map(function($mhs) {
                return [
                    'id_registrasi_mahasiswa' => $mhs->id_registrasi_mahasiswa,
                    'nim' => $mhs->nim,
                    'nama_mahasiswa' => $mhs->nama_mahasiswa,
                    'id_periode' => $mhs->nama_periode_masuk ?? '-',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX: Tambah Mahasiswa ke Kelas (InsertPesertaKelasKuliah)
     */
    public function storePeserta(Request $request)
    {
        $id_kelas = $request->input('id_kelas_kuliah');
        $mahasiswa_ids = $request->input('id_registrasi_mahasiswa', []); // array

        if (empty($mahasiswa_ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada mahasiswa yang dipilih.']);
        }

        $successCount = 0;
        $errors = [];

        foreach ($mahasiswa_ids as $id_reg) {
            try {
                $record = [
                    'id_kelas_kuliah' => $id_kelas,
                    'id_registrasi_mahasiswa' => $id_reg
                ];
                $response = $this->feeder->post('InsertPesertaKelasKuliah', $record);

                if (isset($response['error_code']) && $response['error_code'] == 0) {
                    $successCount++;
                } else {
                    $errors[] = $response['error_desc'] ?? 'Unknown Error';
                }
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return response()->json([
            'success' => $successCount > 0,
            'message' => "Berhasil memasukkan $successCount mahasiswa ke dalam kelas.",
            'errors' => $errors
        ]);
    }

    /**
     * AJAX: Hapus Mahasiswa dari Kelas (DeletePesertaKelasKuliah)
     */
    public function destroyPeserta(Request $request)
    {
        $id_kelas = $request->input('id_kelas_kuliah');
        $id_reg = $request->input('id_registrasi_mahasiswa');

        try {
            $key = [
                'id_kelas_kuliah' => $id_kelas,
                'id_registrasi_mahasiswa' => $id_reg
            ];
            $response = $this->feeder->delete('DeletePesertaKelasKuliah', $key);

            if (isset($response['error_code']) && $response['error_code'] == 0) {
                return response()->json(['success' => true, 'message' => 'Berhasil menghapus mahasiswa dari kelas.']);
            }
            return response()->json(['success' => false, 'message' => $response['error_desc'] ?? 'Gagal menghapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX: Simpan Nilai (UpdateNilaiPerkuliahanKelas) secara massal/kolektif
     */
    public function updateNilai(Request $request)
    {
        $id_kelas = $request->input('id_kelas_kuliah');
        $nilai_data = $request->input('nilai_data', []); // Array of { id_reg, nilai_angka, nilai_huruf, nilai_indeks }

        if (empty($nilai_data)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data nilai yang dikirim.']);
        }

        $successCount = 0;
        $errors = [];

        foreach ($nilai_data as $item) {
            try {
                $id_reg = $item['id_registrasi_mahasiswa'] ?? ($item['id_mahasiswa'] ?? null);
                
                if (!$id_reg) {
                    $errors[] = "Gagal memproses baris: ID Mahasiswa tidak ditemukan (silakan refresh halaman).";
                    continue;
                }

                $key = [
                    'id_kelas_kuliah' => $id_kelas,
                    'id_registrasi_mahasiswa' => $id_reg
                ];
                $record = [
                    'nilai_angka' => (string)$item['nilai_angka'],
                    'nilai_huruf' => $item['nilai_huruf'],
                    'nilai_indeks' => (string)$item['nilai_indeks']
                ];

                // Wait, FeederService post() function is:
                // public function post($act, $record) { 'act'=>$act, 'token'=>$token, 'record'=>$record }
                // For Update we need 'key' and 'record'. So I'll use facade directly.
                
                $payload = [
                    'act' => 'UpdateNilaiPerkuliahanKelas',
                    'token' => $this->feeder->getToken(),
                    'key' => $key,
                    'record' => $record
                ];

                $httpRes = \Illuminate\Support\Facades\Http::post(config('feeder.url'), $payload)->json();

                if (isset($httpRes['error_code']) && $httpRes['error_code'] == 0) {
                    $successCount++;
                } else {
                    $errors[] = "ID " . $id_reg . ": " . ($httpRes['error_desc'] ?? 'Unknown Error');
                }
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return response()->json([
            'success' => $successCount > 0,
            'message' => "Berhasil menyimpan nilai untuk $successCount mahasiswa.",
            'errors' => $errors
        ]);
    }
}
