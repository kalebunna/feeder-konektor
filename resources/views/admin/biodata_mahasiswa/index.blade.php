@extends('templates.layout')

@section('title', 'Biodata Mahasiswa')
@section('page-title', 'Biodata Mahasiswa')

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
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Biodata Mahasiswa</h4>
                    <button id="btn-sync" class="btn btn-info btn-sm">
                        <i class="fas fa-sync-alt"></i> Sinkronkan Data
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive nowrap w-100" id="mahasiswa-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>L/P</th>
                                    <th>Tempat Lahir</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Agama</th>
                                    <th>NIK</th>
                                    <th>NISN</th>
                                    <th>Status Sync</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
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
    <script src="{{ asset('templates/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}">
    </script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('templates/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#mahasiswa-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('biodata-mahasiswa.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_mahasiswa',
                        name: 'nama_mahasiswa'
                    },
                    {
                        data: 'jenis_kelamin',
                        name: 'jenis_kelamin'
                    },
                    {
                        data: 'tempat_lahir',
                        name: 'tempat_lahir'
                    },
                    {
                        data: 'tanggal_lahir',
                        name: 'tanggal_lahir'
                    },
                    {
                        data: 'nama_agama',
                        name: 'nama_agama'
                    },
                    {
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        data: 'nisn',
                        name: 'nisn'
                    },
                    {
                        data: 'status_sync',
                        name: 'status_sync',
                        render: function(data) {
                            let badgeClass = data === 'sudah sync' ? 'bg-success' :
                                'bg-warning text-dark';
                            return `<span class="badge ${badgeClass}">${data}</span>`;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#btn-sync').on('click', function() {
                Swal.fire({
                    title: 'Sinkronisasi Data',
                    text: 'Apakah Anda yakin ingin menyinkronkan data biodata mahasiswa dengan NeoFeeder?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Sinkronkan!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            url: "{{ route('biodata-mahasiswa.sync') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}"
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
                        });
                        table.ajax.reload();
                    }
                });
            });
        });
    </script>
@endpush
