<?php

namespace App\Http\Controllers;

use App\Models\MahasiswaBaruExcel;
use App\Models\Agama;
use App\Models\Negara;
use App\Models\Wilayah;
use App\Models\Pekerjaan;
use App\Models\JenjangDidik;
use App\Models\Prodi;
use App\Models\ProfilPT;
use App\Models\JenisPendaftaran;
use App\Models\JalurMasuk;
use App\Models\Penghasilan;
use App\Models\Pembiayaan;
use App\Models\Semester;
use App\Services\FeederService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportMahasiswaController extends Controller
{
    protected $feeder;

    public function __construct(FeederService $feeder)
    {
        $this->feeder = $feeder;
    }

    public function index()
    {
        $data = MahasiswaBaruExcel::latest()->paginate(20);
        return view('admin.import.mahasiswa', compact('data'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        // Skip header
        $header = fgetcsv($handle, 0, ';');

        $count = 0;
        DB::beginTransaction();
        try {
            // Helper to parse birthdate (e.g. 06/06/81)
            $parseDate = function ($dateStr) {
                if (empty($dateStr) || strlen($dateStr) < 5) return null;
                try {
                    // Assuming DMY
                    return Carbon::createFromFormat('d/m/y', $dateStr)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            };

            while (($row = fgetcsv($handle, 0, ';')) !== FALSE) {
                if (empty($row[1])) continue; // Skip empty names

                MahasiswaBaruExcel::create([
                    'no' => $row[0] ?? null,
                    'nama' => $row[1] ?? null,
                    'nim' => $row[2] ?? null,
                    'tetala' => $row[3] ?? null,
                    'nik' => $row[4] ?? null,
                    'program_studi' => $row[5] ?? null,
                    'tahun_masuk' => $row[6] ?? null,
                    'agama' => $row[7] ?? null,
                    'jenis_kelamin' => $row[8] ?? null,
                    'nomor_hp' => $row[9] ?? null,
                    'rt_rw' => $row[10] ?? null,
                    'dusun' => $row[11] ?? null,
                    'desa_kelurahan' => $row[12] ?? null,
                    'kecamatan' => $row[13] ?? null,
                    'kabupaten' => $row[14] ?? null,

                    'nama_ayah' => $row[15] ?? null,
                    'tetala_ayah' => $row[16] ?? null,
                    'tanggal_lahir_ayah' => $parseDate($row[16] ?? null),
                    'nik_ayah' => $row[17] ?? null,
                    'pendidikan_ayah' => $row[18] ?? null,
                    'rt_rw_ayah' => $row[19] ?? null,
                    'dusun_ayah' => $row[20] ?? null,
                    'desa_kelurahan_ayah' => $row[21] ?? null,
                    'kecamatan_ayah' => $row[22] ?? null,
                    'kabupaten_ayah' => $row[23] ?? null,
                    'pekerjaan_ayah' => $row[24] ?? null,

                    'nama_ibu' => $row[25] ?? null,
                    'tetala_ibu' => $row[26] ?? null,
                    'tanggal_lahir_ibu' => $parseDate($row[26] ?? null),
                    'nik_ibu' => $row[27] ?? null,
                    'pendidikan_ibu' => $row[28] ?? null,
                    'rt_rw_ibu' => $row[29] ?? null,
                    'dusun_ibu' => $row[30] ?? null,
                    'desa_kelurahan_ibu' => $row[31] ?? null,
                    'kecamatan_ibu' => $row[32] ?? null,
                    'kabupaten_ibu' => $row[33] ?? null,
                    'nomor_hp_ibu' => $row[34] ?? null,

                    'nama_wali' => $row[35] ?? null,
                    'alamat_wali' => $row[36] ?? null,
                    'nomor_hp_wali' => $row[37] ?? null,

                    'asal_sekolah' => $row[38] ?? null,
                    'alamat_sekolah' => $row[39] ?? null,
                    'tahun_lulus' => $row[40] ?? null,
                    'nisn' => $row[41] ?? null,
                    'status' => 'belum sync'
                ]);
                $count++;
            }
            DB::commit();
            fclose($handle);

            return back()->with('success', "Berhasil mengimpor $count data mahasiswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }

    public function syncForm($id)
    {
        $mhs = MahasiswaBaruExcel::findOrFail($id);

        // Reference Data
        $agamas = Agama::all();
        $negaras = Negara::all();
        $pekerjaans = Pekerjaan::all();
        $pendidikans = JenjangDidik::all();
        $prodis = Prodi::all();
        $jenisPendaftarans = JenisPendaftaran::all();
        $jalurMasuks = JalurMasuk::all();
        $penghasilans = Penghasilan::all();

        // 1. Auto-match Agama
        $selectedAgama = null;
        if ($mhs->agama) {
            $matchAgama = Agama::where('nama_agama', 'like', '%' . $mhs->agama . '%')->first();
            $selectedAgama = $matchAgama ? $matchAgama->id_agama : null;
        }

        // 2. Auto-match Wilayah (Kecamatan)
        $selectedWilayah = null;
        $matchedWilayah = null;
        if ($mhs->kecamatan) {
            $matchedWilayah = Wilayah::where('nama_wilayah', 'like', '%' . $mhs->kecamatan . '%')
                ->where('id_level_wilayah', 3) // Assuming 3 is Kecamatan
                ->first();
            $selectedWilayah = $matchedWilayah ? $matchedWilayah->id_wilayah : null;
        }

        // 3. Auto-match Negara (Default Indonesia)
        $selectedNegara = 'ID';

        // Helper for terminology matching (Education)
        $matchEdu = function ($value) {
            if (!$value) return null;
            $v = strtoupper($value);
            if (str_contains($v, 'SLTP') || str_contains($v, 'SMP')) return JenjangDidik::where('nama_jenjang_didik', 'like', '%SMP%')->first()?->id_jenjang_didik;
            if (str_contains($v, 'SLTA') || str_contains($v, 'SMA') || str_contains($v, 'MA')) return JenjangDidik::where('nama_jenjang_didik', 'like', '%SMA%')->first()?->id_jenjang_didik;
            if (str_contains($v, 'SD')) return JenjangDidik::where('nama_jenjang_didik', 'like', '%SD%')->first()?->id_jenjang_didik;
            return JenjangDidik::where('nama_jenjang_didik', 'like', '%' . $value . '%')->first()?->id_jenjang_didik;
        };

        // Helper for terminology matching (Occupation)
        $matchJob = function ($value) {
            if (!$value) return null;
            // Handle common Excel patterns like "Petani/Pekebun"
            $parts = explode('/', $value);
            $firstPart = trim($parts[0]);

            $match = Pekerjaan::where('nama_pekerjaan', 'like', '%' . $firstPart . '%')
                ->orWhereRaw('? like concat(\'%\', nama_pekerjaan, \'%\')', [$firstPart])
                ->first();

            return $match ? $match->id_pekerjaan : null;
        };

        // Helper for terminology matching (Income) - Hard to match accurately without raw data, but let's try
        $matchIncome = function ($value) {
            if (!$value) return null;
            // Default to "Tidak Berpenghasilan" or similar if looks like it
            return null;
        };

        // 4. Auto-match Pekerjaan Ayah
        $selectedPekerjaanAyah = $matchJob($mhs->pekerjaan_ayah);

        // Pekerjaan Ibu
        $selectedPekerjaanIbu = $matchJob($mhs->pekerjaan_ibu);

        // Pendidikan Ayah
        $selectedPendidikanAyah = $matchEdu($mhs->pendidikan_ayah);

        // Pendidikan Ibu
        $selectedPendidikanIbu = $matchEdu($mhs->pendidikan_ibu);

        // 5. Parse Birth Date (e.g., "Sumenep, 11 Oktober 2007")
        $tanggalLahir = null;
        if ($mhs->tetala && strpos($mhs->tetala, ',') !== false) {
            $parts = explode(',', $mhs->tetala);
            $dateStr = trim($parts[1]);

            $months = [
                'Januari' => '01',
                'Februari' => '02',
                'Maret' => '03',
                'April' => '04',
                'Mei' => '05',
                'Juni' => '06',
                'Juli' => '07',
                'Agustus' => '08',
                'September' => '09',
                'Oktober' => '10',
                'November' => '11',
                'Desember' => '12'
            ];

            foreach ($months as $name => $num) {
                if (strpos($dateStr, $name) !== false) {
                    $dateStr = str_replace($name, $num, $dateStr);
                    break;
                }
            }

            try {
                $tanggalLahir = Carbon::createFromFormat('d m Y', $dateStr)->format('Y-m-d');
            } catch (\Exception $e) {
                $tanggalLahir = null;
            }
        }

        // Generate default email: name(nospaces/special) + birthyear + @stainas.ac.id
        $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $mhs->nama));
        $birthYear = $tanggalLahir ? Carbon::parse($tanggalLahir)->format('Y') : date('Y');
        $defaultEmail = $cleanName . $birthYear . '@stainas.ac.id';

        $pembiayaans = Pembiayaan::all();
        $semesters = Semester::where('a_periode_aktif', '1')
            ->orderBy('id_semester', 'desc')
            ->get();
        $profilPT = ProfilPT::first();

        return view('admin.import.sync_form', compact(
            'mhs',
            'agamas',
            'negaras',
            'pekerjaans',
            'pendidikans',
            'prodis',
            'jenisPendaftarans',
            'jalurMasuks',
            'penghasilans',
            'pembiayaans',
            'semesters',
            'profilPT',
            'selectedAgama',
            'selectedWilayah',
            'matchedWilayah',
            'selectedNegara',
            'selectedPekerjaanAyah',
            'selectedPekerjaanIbu',
            'selectedPendidikanAyah',
            'selectedPendidikanIbu',
            'tanggalLahir',
            'defaultEmail'
        ));
    }

    public function syncProcess(Request $request, $id)
    {
        $mhs = MahasiswaBaruExcel::findOrFail($id);

        try {
            // 1. Insert Biodata Mahasiswa
            $biodataRecord = [
                'nama_mahasiswa' => $request->nama_mahasiswa,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'id_agama' => $request->id_agama ? (int)$request->id_agama : null,
                'nik' => $request->nik,
                'nisn' => $mhs->nisn,
                'npwp' => null,
                'kewarganegaraan' => $request->kewarganegaraan,
                'jalan' => null,
                'dusun' => $request->dusun,
                'rt' => (int)$request->rt,
                'rw' => (int)$request->rw,
                'kelurahan' => $request->kelurahan,
                'kode_pos' => null,
                'id_wilayah' => $request->id_wilayah,
                'id_jenis_tinggal' => 1,
                'id_alat_transportasi' => null,
                'telepon' => null,
                'handphone' => $request->handphone,
                'email' => $request->email,
                'penerima_kps' => 0,
                'nomor_kps' => null,
                'nik_ayah' => $request->nik_ayah,
                'nama_ayah' => $request->nama_ayah,
                'tanggal_lahir_ayah' => $request->tanggal_lahir_ayah,
                'id_pendidikan_ayah' => $request->id_pendidikan_ayah ? (int)$request->id_pendidikan_ayah : null,
                'id_pekerjaan_ayah' => $request->id_pekerjaan_ayah ? (int)$request->id_pekerjaan_ayah : null,
                'id_penghasilan_ayah' => $request->id_penghasilan_ayah ? (int)$request->id_penghasilan_ayah : null,
                'nik_ibu' => $request->nik_ibu,
                'nama_ibu_kandung' => $request->nama_ibu_kandung,
                'tanggal_lahir_ibu' => $request->tanggal_lahir_ibu,
                'id_pendidikan_ibu' => $request->id_pendidikan_ibu ? (int)$request->id_pendidikan_ibu : null,
                'id_pekerjaan_ibu' => $request->id_pekerjaan_ibu ? (int)$request->id_pekerjaan_ibu : null,
                'id_penghasilan_ibu' => $request->id_penghasilan_ibu ? (int)$request->id_penghasilan_ibu : null,
                'nama_wali' => null,
                'tanggal_lahir_wali' => null,
                'id_pendidikan_wali' => null,
                'id_pekerjaan_wali' => null,
                'id_penghasilan_wali' => null,
                'id_kebutuhan_khusus_mahasiswa' => 0,
                'id_kebutuhan_khusus_ayah' => 0,
                'id_kebutuhan_khusus_ibu' => 0,
            ];

            $responseBio = $this->feeder->post('InsertBiodataMahasiswa', $biodataRecord);

            if (isset($responseBio['error_code']) && $responseBio['error_code'] != 0) {
                return response()->json(['success' => false, 'message' => 'Feeder Error (Biodata): ' . $responseBio['error_desc']]);
            }

            $id_mahasiswa = $responseBio['data']['id_mahasiswa'];

            // 2. Insert Riwayat Pendidikan Mahasiswa
            $regRecord = [
                'id_mahasiswa' => $id_mahasiswa,
                'nim' => $request->nim,
                'id_jenis_daftar' => (int)$request->id_jenis_daftar,
                'id_jalur_daftar' => (int)$request->id_jalur_daftar,
                'id_periode_masuk' => $request->id_periode_masuk,
                'tanggal_daftar' => $request->tanggal_daftar,
                'id_perguruan_tinggi' => $request->id_perguruan_tinggi,
                'id_prodi' => $request->id_prodi,
                'biaya_masuk' => (float)$request->biaya_masuk,
            ];

            if ($request->id_pembiayaan) {
                $regRecord['id_pembiayaan'] = $request->id_pembiayaan;
            }

            $responseReg = $this->feeder->post('InsertRiwayatPendidikanMahasiswa', $regRecord);

            if (isset($responseReg['error_code']) && $responseReg['error_code'] != 0) {
                // Rollback: Delete previously created biodata
                $this->feeder->post('DeleteBiodataMahasiswa', ['id_mahasiswa' => $id_mahasiswa]);

                return response()->json(['success' => false, 'message' => 'Feeder Error (Registrasi): ' . $responseReg['error_desc'] . '. (Biodata dihapus kembali)']);
            }

            $mhs->update(['status' => 'sudah sync']);

            return response()->json(['success' => true, 'message' => 'Berhasil sinkronisasi data ' . $mhs->nama . ' ke Feeder.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
