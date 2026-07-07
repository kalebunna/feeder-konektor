<?php

namespace App\Http\Controllers;

use App\Models\BiodataMahasiswa;
use App\Models\Mahasiswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Services\FeederService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

            // Filter by Program Studi (Multiple)
            if ($request->has('prodi_names') && !empty($request->prodi_names)) {
                $data->whereHas('mahasiswa', function ($q) use ($request) {
                    $q->whereIn('nama_program_studi', $request->prodi_names);
                });
            }

            // Filter by Angkatan / Periode (Multiple)
            if ($request->has('id_periodes') && !empty($request->id_periodes)) {
                $idPeriodes = $request->id_periodes;
                $data->whereHas('mahasiswa', function ($q) use ($idPeriodes) {
                    $q->where(function ($sq) use ($idPeriodes) {
                        foreach ($idPeriodes as $year) {
                            $sq->orWhere('id_periode', 'like', $year . '%');
                        }
                    });
                });
            }

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

        $prodis = Mahasiswa::distinct()->whereNotNull('nama_program_studi')->orderBy('nama_program_studi')->get(['nama_program_studi']);
        $periodes = TahunAjaran::orderBy('id_tahun_ajaran', 'desc')->get(['id_tahun_ajaran', 'nama_tahun_ajaran']);

        return view('admin.biodata_mahasiswa.index', compact('prodis', 'periodes'));
    }

    public function export(Request $request)
    {
        $data = BiodataMahasiswa::query()->with('mahasiswa');

        // Apply filters
        $prodiNames = $request->prodi_names;
        if (!empty($prodiNames)) {
            $data->whereHas('mahasiswa', function ($q) use ($prodiNames) {
                $q->whereIn('nama_program_studi', $prodiNames);
            });
        }

        $idPeriodes = $request->id_periodes;
        if (!empty($idPeriodes)) {
            $data->whereHas('mahasiswa', function ($q) use ($idPeriodes) {
                $q->where(function ($sq) use ($idPeriodes) {
                    foreach ($idPeriodes as $year) {
                        $sq->orWhere('id_periode', 'like', $year . '%');
                    }
                });
            });
        }

        $biodata = $data->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Biodata Mahasiswa');

        // Title Block
        $sheet->setCellValue('A1', 'BIODATA MAHASISWA STAINAS');
        
        // Metadata
        $prodiText = !empty($prodiNames) ? implode(', ', $prodiNames) : 'Semua';
        $angkatanText = '';
        if (!empty($idPeriodes)) {
            $angkatanText = implode(', ', $idPeriodes);
        } else {
            $angkatanText = 'Semua';
        }

        $sheet->setCellValue('A3', 'Program Studi: ' . $prodiText);
        $sheet->setCellValue('A4', 'Angkatan: ' . $angkatanText);
        $sheet->setCellValue('A5', 'Tanggal Unduh: ' . Carbon::now()->translatedFormat('d F Y H:i'));

        $sheet->getStyle('A3:A5')->getFont()->setItalic(true);

        // Table Header
        $headers = [
            'No', 'NIM', 'Nama Mahasiswa', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Agama', 
            'NIK', 'NISN', 'NPWP', 'Kewarganegaraan', 'Jalan', 'Dusun', 'RT', 'RW', 
            'Kelurahan', 'Kode Pos', 'Kecamatan (Wilayah)', 'Jenis Tinggal', 'Alat Transportasi', 
            'Telepon', 'Handphone', 'Email', 'Penerima KPS', 'Nomor KPS', 'Program Studi', 'Periode Masuk',
            'NIK Ayah', 'Nama Ayah', 'Tanggal Lahir Ayah', 'Pendidikan Ayah', 'Pekerjaan Ayah', 'Penghasilan Ayah',
            'NIK Ibu', 'Nama Ibu Kandung', 'Tanggal Lahir Ibu', 'Pendidikan Ibu', 'Pekerjaan Ibu', 'Penghasilan Ibu',
            'Nama Wali', 'Tanggal Lahir Wali', 'Pendidikan Wali', 'Pekerjaan Wali', 'Penghasilan Wali',
            'Kebutuhan Khusus Mahasiswa', 'Kebutuhan Khusus Ayah', 'Kebutuhan Khusus Ibu', 'Status Sync'
        ];

        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->mergeCells("A1:{$lastColLetter}1");
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $startRow = 7;
        foreach ($headers as $colIdx => $headerText) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheet->setCellValue($colLetter . $startRow, $headerText);
        }

        // Style header
        $headerRange = 'A' . $startRow . ':' . $lastColLetter . $startRow;
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowIdx = $startRow + 1;
        $no = 1;
        foreach ($biodata as $item) {
            $nim = $item->mahasiswa ? $item->mahasiswa->nim : '-';
            $prodi = $item->mahasiswa ? $item->mahasiswa->nama_program_studi : '-';
            $periode = $item->mahasiswa ? $item->mahasiswa->nama_periode_masuk : '-';
            
            $values = [
                $no++,
                $nim,
                $item->nama_mahasiswa,
                $item->jenis_kelamin == 'P' ? 'Perempuan' : ($item->jenis_kelamin == 'L' ? 'Laki-laki' : $item->jenis_kelamin),
                $item->tempat_lahir,
                $item->tanggal_lahir ? Carbon::parse($item->tanggal_lahir)->translatedFormat('d F Y') : '-',
                $item->nama_agama,
                $item->nik,
                $item->nisn,
                $item->npwp,
                $item->kewarganegaraan,
                $item->jalan,
                $item->dusun,
                $item->rt,
                $item->rw,
                $item->kelurahan,
                $item->kode_pos,
                $item->nama_wilayah,
                $item->nama_jenis_tinggal,
                $item->nama_alat_transportasi,
                $item->telepon,
                $item->handphone,
                $item->email,
                $item->penerima_kps == 1 || $item->penerima_kps == '1' ? 'Ya' : 'Tidak',
                $item->nomor_kps,
                $prodi,
                $periode,
                $item->nik_ayah,
                $item->nama_ayah,
                $item->tanggal_lahir_ayah ? Carbon::parse($item->tanggal_lahir_ayah)->translatedFormat('d F Y') : '-',
                $item->nama_pendidikan_ayah,
                $item->nama_pekerjaan_ayah,
                $item->nama_penghasilan_ayah,
                $item->nik_ibu,
                $item->nama_ibu_kandung,
                $item->tanggal_lahir_ibu ? Carbon::parse($item->tanggal_lahir_ibu)->translatedFormat('d F Y') : '-',
                $item->nama_pendidikan_ibu,
                $item->nama_pekerjaan_ibu,
                $item->nama_penghasilan_ibu,
                $item->nama_wali,
                $item->tanggal_lahir_wali ? Carbon::parse($item->tanggal_lahir_wali)->translatedFormat('d F Y') : '-',
                $item->nama_pendidikan_wali,
                $item->nama_pekerjaan_wali,
                $item->nama_penghasilan_wali,
                $item->nama_kebutuhan_khusus_mahasiswa,
                $item->nama_kebutuhan_khusus_ayah,
                $item->nama_kebutuhan_khusus_ibu,
                $item->status_sync
            ];

            foreach ($values as $colIdx => $val) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                // Clean strings / prevent formula injection or science notation for large numbers
                if (in_array($colIdx, [1, 7, 8, 9, 13, 14, 16, 20, 21, 24, 27, 33])) {
                    $sheet->setCellValueExplicit($colLetter . $rowIdx, $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($colLetter . $rowIdx, $val);
                }
            }

            $rowIdx++;
        }

        // Apply borders and alignment to table data
        $tableRange = 'A' . $startRow . ':' . $lastColLetter . ($rowIdx - 1);
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle($tableRange)->applyFromArray($styleArray);

        // Autofit columns
        foreach (range(1, count($headers)) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Generate download stream
        $filename = 'biodata_mahasiswa_' . date('Ymd_His') . '.xlsx';
        
        return response()->stream(
            function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
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
