@extends('templates.layout')

@section('title', 'Input Nilai & KRS')
@section('page-title', 'Daftar Kelas Kuliah')

@push('css')
    <!-- DataTables -->
    <link href="{{ asset('templates/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Kelas Kuliah Semester Berjalan</h4>
                    <p class="card-title-desc">Semester Aktif: <strong>{{ $activeSemester->nama_semester ?? '-' }}</strong>. Pilih kelas di bawah ini untuk mengelola Peserta Kelas (KRS) dan menginput nilai secara kolektif.</p>
                </div>
                <div class="card-body">
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="filterProdi" class="form-label">Filter Program Studi:</label>
                            <select id="filterProdi" class="form-select">
                                <option value="">Semua Program Studi</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0" id="kelasTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%">No</th>
                                    <th>Program Studi</th>
                                    <th>Mata Kuliah</th>
                                    <th class="text-center">Nama Kelas</th>
                                    <th class="text-center">Kapasitas</th>
                                    <th class="text-center" style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kelas as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $item['nama_program_studi'] ?? '-' }}</td>
                                        <td>{{ $item['nama_mata_kuliah'] ?? '-' }}</td>
                                        <td class="text-center fw-bold">{{ $item['nama_kelas_kuliah'] ?? '-' }}</td>
                                        <td class="text-center">{{ $item['kapasitas'] ?? '-' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('input-nilai.show', $item['id_kelas_kuliah']) }}" class="btn btn-sm btn-primary">
                                                <i class="bx bx-edit-alt align-middle me-1"></i> Kelola KRS & Nilai
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Tidak ada data kelas kuliah untuk semester ini di Feeder. Pastikan Anda telah meng-generate kelas.
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

@push('js')
    <!-- Required datatable js -->
    <script src="{{ asset('templates/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('templates/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('templates/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('templates/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            var table = $('#kelasTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100, 500],
                "language": {
                    "search": "Cari Kelas/Matkul:"
                },
                initComplete: function () {
                    // Populate the dropdown with unique Prodi
                    this.api().column(1).every(function () {
                        var column = this;
                        var select = $('#filterProdi');
                        column.data().unique().sort().each(function (d, j) {
                            if(d !== '-' && d !== '') {
                                select.append('<option value="'+d+'">'+d+'</option>')
                            }
                        });
                    });
                }
            });

            // Event listener for the Prodi filter dropdown
            $('#filterProdi').on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                table.column(1)
                     .search(val ? '^'+val+'$' : '', true, false)
                     .draw();
            });
        });
    </script>
@endpush
