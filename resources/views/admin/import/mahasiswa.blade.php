@extends('templates.layout')

@section('title', 'Import Mahasiswa Baru')
@section('page-title', 'Import Mahasiswa Baru')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Unggah File Excel/CSV</h4>
                    <p class="card-title-desc">Pastikan format file sesuai dengan template yang ditentukan (Pemisah Titik
                        Koma / Semi-colon).</p>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-all me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-block-helper me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('import-mahasiswa.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="file" class="form-label">Pilih File CSV/Excel</label>
                                    <input class="form-control @error('file') is-invalid @enderror" type="file"
                                        id="file" name="file" accept=".csv, .txt, .xlsx">
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary w-md">
                                    <i class="fas fa-upload me-1"></i> Impor Data
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Data Terakhir Diimpor</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Program Studi</th>
                                    <th>Tahun Masuk</th>
                                    <th>Status Sync</th>
                                    <th>Tanggal Impor</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration + ($data->firstItem() - 1) }}</td>
                                        <td>{{ $item->nim }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->program_studi }}</td>
                                        <td>{{ $item->tahun_masuk }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $item->status == 'sudah sync' ? 'bg-success' : 'bg-warning' }}">
                                                {{ strtoupper($item->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if ($item->status == 'belum sync')
                                                <a href="{{ route('import-mahasiswa.sync-form', $item->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-sync me-1"></i> Sinkronkan
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-secondary" disabled>
                                                    <i class="fas fa-check me-1"></i> Selesai
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada data diimpor.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
