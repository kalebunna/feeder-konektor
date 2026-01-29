<?php

namespace App\Http\Controllers;

use App\Services\FeederService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Agama;
use App\Models\IkatanKerja;
use App\Models\JalurMasuk;
use App\Models\JenisEvaluasi;
use App\Models\JenisKeluar;
use App\Models\JenisPendaftaran;
use App\Models\JenisTinggal;
use App\Models\JenjangDidik;
use App\Models\LevelWilayah;
use App\Models\Negara;
use App\Models\Pekerjaan;
use App\Models\StatusMahasiswa;
use App\Models\Wilayah;
use App\Models\Prodi;
use App\Models\ProfilPT;
use App\Models\Penghasilan;
use App\Models\Pembiayaan;

class ReferenceController extends Controller
{
    protected $feeder;

    public function __construct(FeederService $feeder)
    {
        $this->feeder = $feeder;
    }

    public function index()
    {
        $references = [
            ['name' => 'Agama', 'table' => 'agamas', 'model' => Agama::class, 'act' => 'GetAgama'],
            ['name' => 'Ikatan Kerja', 'table' => 'ikatan_kerja', 'model' => IkatanKerja::class, 'act' => 'GetIkatanKerja'],
            ['name' => 'Jalur Masuk', 'table' => 'jalur_masuk', 'model' => JalurMasuk::class, 'act' => 'GetJalurMasuk'],
            ['name' => 'Jenis Evaluasi', 'table' => 'jenis_evaluasi', 'model' => JenisEvaluasi::class, 'act' => 'GetJenisEvaluasi'],
            ['name' => 'Jenis Keluar', 'table' => 'jenis_keluar', 'model' => JenisKeluar::class, 'act' => 'GetJenisKeluar'],
            ['name' => 'Jenis Pendaftaran', 'table' => 'jenis_pendaftaran', 'model' => JenisPendaftaran::class, 'act' => 'GetJenisPendaftaran'],
            ['name' => 'Jenis Tinggal', 'table' => 'jenis_tinggal', 'model' => JenisTinggal::class, 'act' => 'GetJenisTinggal'],
            ['name' => 'Jenjang Didik', 'table' => 'jenjang_didik', 'model' => JenjangDidik::class, 'act' => 'GetJenjangPendidikan'],
            ['name' => 'Level Wilayah', 'table' => 'level_wilayah', 'model' => LevelWilayah::class, 'act' => 'GetLevelWilayah'],
            ['name' => 'Negara', 'table' => 'negara', 'model' => Negara::class, 'act' => 'GetNegara'],
            ['name' => 'Pekerjaan', 'table' => 'pekerjaan', 'model' => Pekerjaan::class, 'act' => 'GetPekerjaan'],
            ['name' => 'Status Mahasiswa', 'table' => 'status_mahasiswa', 'model' => StatusMahasiswa::class, 'act' => 'GetStatusMahasiswa'],
            ['name' => 'Wilayah', 'table' => 'wilayah', 'model' => Wilayah::class, 'act' => 'GetWilayah'],
            ['name' => 'Program Studi', 'table' => 'prodis', 'model' => Prodi::class, 'act' => 'GetProdi'],
            ['name' => 'Profil Perguruan Tinggi', 'table' => 'profil_pts', 'model' => ProfilPT::class, 'act' => 'GetProfilPT'],
            ['name' => 'Penghasilan', 'table' => 'penghasilans', 'model' => Penghasilan::class, 'act' => 'GetPenghasilan'],
            ['name' => 'Pembiayaan', 'table' => 'pembiayaans', 'model' => Pembiayaan::class, 'act' => 'GetPembiayaan'],
        ];

        return view('admin.reference.index', compact('references'));
    }

    public function sync($table)
    {
        $maps = [
            'agamas' => ['model' => Agama::class, 'act' => 'GetAgama', 'pk' => 'id_agama'],
            'ikatan_kerja' => ['model' => IkatanKerja::class, 'act' => 'GetIkatanKerjaSdm', 'pk' => 'id_ikatan_kerja'],
            'jalur_masuk' => ['model' => JalurMasuk::class, 'act' => 'GetJalurMasuk', 'pk' => 'id_jalur_masuk'],
            'jenis_evaluasi' => ['model' => JenisEvaluasi::class, 'act' => 'GetJenisEvaluasi', 'pk' => 'id_jenis_evaluasi'],
            'jenis_keluar' => ['model' => JenisKeluar::class, 'act' => 'GetJenisKeluar', 'pk' => 'id_jenis_keluar'],
            'jenis_pendaftaran' => ['model' => JenisPendaftaran::class, 'act' => 'GetJenisPendaftaran', 'pk' => 'id_jenis_daftar'],
            'jenis_tinggal' => ['model' => JenisTinggal::class, 'act' => 'GetJenisTinggal', 'pk' => 'id_jenis_tinggal'],
            'jenjang_didik' => ['model' => JenjangDidik::class, 'act' => 'GetJenjangPendidikan', 'pk' => 'id_jenjang_didik'],
            'level_wilayah' => ['model' => LevelWilayah::class, 'act' => 'GetLevelWilayah', 'pk' => 'id_level_wilayah'],
            'negara' => ['model' => Negara::class, 'act' => 'GetNegara', 'pk' => 'id_negara'],
            'pekerjaan' => ['model' => Pekerjaan::class, 'act' => 'GetPekerjaan', 'pk' => 'id_pekerjaan'],
            'status_mahasiswa' => ['model' => StatusMahasiswa::class, 'act' => 'GetStatusMahasiswa', 'pk' => 'id_status_mahasiswa'],
            'wilayah' => ['model' => Wilayah::class, 'act' => 'GetWilayah', 'pk' => 'id_wilayah'],
            'prodis' => ['model' => Prodi::class, 'act' => 'GetProdi', 'pk' => 'kode_program_studi'],
            'profil_pts' => ['model' => ProfilPT::class, 'act' => 'GetProfilPT', 'pk' => 'kode_perguruan_tinggi'],
            'penghasilans' => ['model' => Penghasilan::class, 'act' => 'GetPenghasilan', 'pk' => 'id_penghasilan'],
            'pembiayaans' => ['model' => Pembiayaan::class, 'act' => 'GetPembiayaan', 'pk' => 'id_pembiayaan'],
        ];

        if (!isset($maps[$table])) {
            return response()->json(['success' => false, 'message' => 'Table not found'], 404);
        }

        $config = $maps[$table];

        try {
            $response = $this->feeder->proxy($config['act']);

            if (isset($response['error_code']) && $response['error_code'] != 0) {
                return response()->json(['success' => false, 'message' => $response['error_desc']]);
            }

            $data = $response['data'];
            $count = 0;

            foreach ($data as $item) {
                // Remove keys not in fillable if necessary, but updateOrCreate handles that
                $config['model']::updateOrCreate(
                    [$config['pk'] => $item[$config['pk']]],
                    $item
                );
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully synced $count records",
                'count' => $config['model']::count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
