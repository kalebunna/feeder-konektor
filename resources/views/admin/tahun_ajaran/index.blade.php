@extends('templates.layout')

@section('title', 'Tahun Ajaran')
@section('page-title', 'Tahun Ajaran')

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
            @if ($activeYear)
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Tahun Ajaran Aktif Saat Ini: <strong>{{ $activeYear->nama_tahun_ajaran }}</strong> (ID:
                    {{ $activeYear->id_tahun_ajaran }})
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @else
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-outline me-2"></i>
                    Belum ada Tahun Ajaran aktif yang tersinkronisasi.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Manajemen Tahun Ajaran</h4>
                        <div>
                            <button id="btn-sync-active" class="btn btn-primary btn-sm">
                                <i class="fas fa-check-circle"></i> Cek Status Aktif Feeder
                            </button>
                        </div>
                    </div>

                    <form id="sync-form" class="row gx-3 gy-2 align-items-center">
                        <div class="col-sm-auto">
                            <label class="fw-bold mb-0">Sinkronisasi Rentang Tahun:</label>
                        </div>
                        <div class="col-sm-2">
                            <div class="input-group input-group-sm">
                                <div class="input-group-text">Dari</div>
                                <input type="number" class="form-control" id="tahun_mulai" name="tahun_mulai"
                                    placeholder="2010" value="{{ date('Y') - 5 }}">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="input-group input-group-sm">
                                <div class="input-group-text">Sampai</div>
                                <input type="number" class="form-control" id="tahun_sampai" name="tahun_sampai"
                                    placeholder="2024" value="{{ date('Y') }}">
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-info btn-sm">
                                <i class="fas fa-sync-alt"></i> Sinkronkan
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive nowrap w-100" id="tahun-ajaran-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Nama Tahun Ajaran</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
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
                    Menurut instruksi, status <strong>Aktif</strong> ditandai dengan <code>a_periode_aktif: "1"</code> dari
                    Neo Feeder.
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
            var table = $('#tahun-ajaran-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('tahun-ajaran.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id_tahun_ajaran',
                        name: 'id_tahun_ajaran'
                    },
                    {
                        data: 'nama_tahun_ajaran',
                        name: 'nama_tahun_ajaran'
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

            $('#sync-form').on('submit', function(e) {
                e.preventDefault();
                let tahunMulai = $('#tahun_mulai').val();
                let tahunSampai = $('#tahun_sampai').val();

                if (!tahunMulai || !tahunSampai) {
                    Swal.fire('Error', 'Tahun mulai dan sampai harus diisi', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Sinkronisasi Data',
                    text: `Sinkronkan data Tahun Ajaran dari ${tahunMulai} sampai ${tahunSampai}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Sinkronkan!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            url: "{{ route('tahun-ajaran.sync') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                tahun_mulai: tahunMulai,
                                tahun_sampai: tahunSampai
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
                    text: 'Cek kesesuaian status periode aktif dengan Neo Feeder?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Cek Sekarang',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            url: "{{ route('tahun-ajaran.sync-active') }}",
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
