@extends('templates.layout')

@section('title', 'Semester')
@section('page-title', 'Semester')

@push('css')
    <!-- DataTables -->
    <link href="{{ asset('templates/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('templates/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
        rel="stylesheet" type="text/css" />
    <!-- SweetAlert2 -->
    <link href="{{ asset('templates/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            @if ($activeSemester)
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Semester Aktif Saat Ini: <strong>{{ $activeSemester->nama_semester }}</strong> (ID:
                    {{ $activeSemester->id_semester }})
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @else
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-outline me-2"></i>
                    Belum ada Semester aktif yang tersinkronisasi.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Manajemen Semester</h4>
                        <div class="d-flex gap-2">
                            <button id="btn-sync-active" class="btn btn-primary btn-sm">
                                <i class="fas fa-check-circle"></i> Cek Status Aktif Feeder
                            </button>
                            <button id="btn-sync" class="btn btn-info btn-sm">
                                <i class="fas fa-sync-alt"></i> Sinkronkan Semester Sejarah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive nowrap w-100" id="semester-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID Semester</th>
                                    <th>Nama Semester</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Semester</th>
                                    <th>Mulai</th>
                                    <th>Selesai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-2">
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Data Semester disinkronkan berdasarkan <strong>Tahun Ajaran</strong> yang sudah ada di database lokal.
                </p>
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
    <script src="{{ asset('templates/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}">
    </script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('templates/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#semester-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('semester.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id_semester',
                        name: 'id_semester'
                    },
                    {
                        data: 'nama_semester',
                        name: 'nama_semester'
                    },
                    {
                        data: 'tahun_ajaran.nama_tahun_ajaran',
                        name: 'tahunAjaran.nama_tahun_ajaran'
                    },
                    {
                        data: 'semester',
                        name: 'semester'
                    },
                    {
                        data: 'tanggal_mulai',
                        name: 'tanggal_mulai'
                    },
                    {
                        data: 'tanggal_selesai',
                        name: 'tanggal_selesai'
                    },
                    {
                        data: 'a_periode_aktif',
                        name: 'a_periode_aktif',
                        className: 'text-center'
                    },
                ]
            });

            $('#btn-sync').on('click', function() {
                Swal.fire({
                    title: 'Sinkronisasi Data',
                    text: `Sinkronkan data Semester berdasarkan Tahun Ajaran yang ada di database?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Sinkronkan!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            url: "{{ route('semester.sync') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                            }
                        }).then(response => {
                            if (!response.success) {
                                throw new Error(response.message ||
                                    'Gagal menyinkronkan data');
                            }
                            return response;
                        }).catch(error => {
                            Swal.showValidationMessage(
                                `Request failed: ${error.message}`);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: result.value.message,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            });

            $('#btn-sync-active').on('click', function() {
                Swal.fire({
                    title: 'Cek Status Aktif',
                    text: 'Cek kesesuaian status semester aktif dengan Neo Feeder?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Cek Sekarang',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            url: "{{ route('semester.sync-active') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}"
                            }
                        }).then(response => {
                            if (!response.success) {
                                throw new Error(response.message ||
                                    'Gagal mengecek status');
                            }
                            return response;
                        }).catch(error => {
                            Swal.showValidationMessage(
                                `Request failed: ${error.message}`);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Selesai!',
                            text: result.value.message,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            });
        });
    </script>
@endpush
