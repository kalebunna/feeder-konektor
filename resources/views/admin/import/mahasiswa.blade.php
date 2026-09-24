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
                            <div class="col-md-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-md">
                                    <i class="fas fa-upload me-1"></i> Impor Data
                                </button>
                                <a href="{{ route('import-mahasiswa.template') }}" class="btn btn-outline-success">
                                    <i class="fas fa-file-excel me-1"></i> Unduh Template CSV
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Data Terakhir Diimpor</h4>
                    @if($data->count() > 0)
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#clearModal">
                            <i class="fas fa-trash-alt me-1"></i> Bersihkan Data
                        </button>
                    @endif
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
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('import-mahasiswa.sync-form', $item->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-sync me-1"></i> Sinkronkan
                                                    </a>
                                                    <form action="{{ route('import-mahasiswa.destroy', $item->id) }}" method="POST" class="d-inline form-delete">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="fas fa-check me-1"></i> Selesai
                                                    </button>
                                                    <a href="{{ route('import-mahasiswa.sync-form', $item->id) }}"
                                                        class="btn btn-sm btn-info" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form action="{{ route('import-mahasiswa.destroy', $item->id) }}" method="POST" class="d-inline form-delete">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
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

    <!-- Modal Clear By Tahun Masuk -->
    <div class="modal fade" id="clearModal" tabindex="-1" aria-labelledby="clearModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('import-mahasiswa.clear-all') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clearModalLabel">Bersihkan Data Impor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            Pilih <strong>Tahun Masuk</strong> yang datanya ingin dihapus secara permanen dari aplikasi ini. Data yang sudah disinkronkan ke Feeder <strong>tidak</strong> akan terhapus di Feeder.
                        </div>
                        <div class="mb-3">
                            <label for="tahun_masuk" class="form-label">Tahun Masuk</label>
                            <select name="tahun_masuk" id="tahun_masuk" class="form-control" required>
                                <option value="">-- Pilih Tahun Masuk --</option>
                                @foreach($tahunMasukList as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.btn-delete').on('click', function() {
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data impor ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
