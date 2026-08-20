@extends('templates.layout')

@section('title', 'Daftar Skala Nilai')
@section('page-title', 'Data Skala Nilai (Bobot Nilai)')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title">Daftar Skala Nilai Prodi</h4>
                        <p class="card-title-desc">Data di bawah ini merupakan hasil sinkronisasi dari Neo Feeder.</p>
                    </div>
                    <a href="{{ route('reference.index') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-sync align-middle me-1"></i> Ke Halaman Sinkronisasi
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%">No</th>
                                    <th>Program Studi</th>
                                    <th class="text-center">Nilai Huruf</th>
                                    <th class="text-center">Nilai Indeks</th>
                                    <th class="text-center">Bobot Min</th>
                                    <th class="text-center">Bobot Max</th>
                                    <th class="text-center">Tgl Mulai</th>
                                    <th class="text-center">Tgl Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($skalaNilais as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $item->nama_program_studi ?? '-' }}</td>
                                        <td class="text-center fw-bold text-primary">{{ $item->nilai_huruf }}</td>
                                        <td class="text-center">{{ number_format($item->nilai_indeks, 2) }}</td>
                                        <td class="text-center">{{ number_format($item->bobot_nilai_min, 2) }}</td>
                                        <td class="text-center">{{ number_format($item->bobot_nilai_maks, 2) }}</td>
                                        <td class="text-center">{{ $item->tanggal_mulai_efektif ? date('d-m-Y', strtotime($item->tanggal_mulai_efektif)) : '-' }}</td>
                                        <td class="text-center">{{ $item->tanggal_akhir_efektif ? date('d-m-Y', strtotime($item->tanggal_akhir_efektif)) : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            Belum ada data skala nilai. Silakan lakukan sinkronisasi terlebih dahulu dari menu <b>Integrasi Feeder > Sinkronisasi</b>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
