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
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-selection--multiple {
            border: 1px solid #ced4da !important;
            min-height: 38px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #5156be !important;
            border: none !important;
            color: white !important;
            padding: 2px 8px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white !important;
            margin-right: 5px !important;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Biodata Mahasiswa</h4>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#filterModal">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <button id="btn-export" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                        <button id="btn-sync" class="btn btn-info btn-sm">
                            <i class="fas fa-sync-alt"></i> Sinkronkan Data
                        </button>
                        <button id="btn-clear-sync" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash-alt"></i> Clear & Sinkron Ulang
                        </button>
                    </div>
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
    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel"><i class="fas fa-filter me-2"></i>Filter Data Biodata Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Periode Masuk / Angkatan</label>
                                <select class="form-control select2-multiple" name="id_periodes[]" multiple="multiple">
                                    @foreach ($periodes as $p)
                                        <option value="{{ $p->nama_periode_masuk }}">{{ $p->nama_periode_masuk }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Bisa pilih lebih dari satu periode</small>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Program Studi</label>
                                <select class="form-control select2-multiple" name="prodi_names[]" multiple="multiple">
                                    @foreach ($prodis as $pr)
                                        <option value="{{ $pr->nama_program_studi }}">{{ $pr->nama_program_studi }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Bisa pilih lebih dari satu prodi</small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btn-reset-filter">Reset Filter</button>
                    <button type="button" class="btn btn-primary" id="btn-apply-filter">Terapkan Filter</button>
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
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize Select2
            $('.select2-multiple').select2({
                placeholder: " Pilih opsi...",
                allowClear: true,
                dropdownParent: $('#filterModal')
            });

            var table = $('#mahasiswa-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('biodata-mahasiswa.index') }}",
                    data: function(d) {
                        d.prodi_names = $('select[name="prodi_names[]"]').val();
                        d.id_periodes = $('select[name="id_periodes[]"]').val();
                    }
                },
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

            $('#btn-apply-filter').on('click', function() {
                table.ajax.reload();
                $('#filterModal').modal('hide');
            });

            $('#btn-reset-filter').on('click', function() {
                $('#filterForm')[0].reset();
                $('.select2-multiple').val(null).trigger('change');
                table.ajax.reload();
                $('#filterModal').modal('hide');
            });

            $('#btn-export').on('click', function() {
                let prodiNames = $('select[name="prodi_names[]"]').val() || [];
                let idPeriodes = $('select[name="id_periodes[]"]').val() || [];
                
                let queryParams = $.param({
                    prodi_names: prodiNames,
                    id_periodes: idPeriodes
                });
                
                window.location.href = "{{ route('biodata-mahasiswa.export') }}?" + queryParams;
            });

             $('#btn-sync').on('click', function() {
                let prodiOptions = {
                    'all': 'Semua Program Studi'
                };
                @foreach($prodiList as $p)
                    prodiOptions["{{ $p->id_prodi }}"] = "{{ $p->nama_program_studi }} ({{ $p->nama_jenjang_pendidikan }})";
                @endforeach

                Swal.fire({
                    title: 'Sinkronisasi Data',
                    text: 'Pilih Program Studi yang ingin disinkronkan dengan NeoFeeder:',
                    input: 'select',
                    inputOptions: prodiOptions,
                    inputValue: 'all',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Sinkronkan!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    showLoaderOnConfirm: true,
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Anda harus memilih Program Studi!'
                        }
                    },
                    preConfirm: (prodiId) => {
                        return $.ajax({
                            url: "{{ route('biodata-mahasiswa.sync') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id_prodi: prodiId
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

            $('#btn-clear-sync').on('click', function() {
                let prodiOptions = {
                    'all': 'Semua Program Studi'
                };
                @foreach($prodiList as $p)
                    prodiOptions["{{ $p->id_prodi }}"] = "{{ $p->nama_program_studi }} ({{ $p->nama_jenjang_pendidikan }})";
                @endforeach

                Swal.fire({
                    title: 'Peringatan Hapus & Sinkron Ulang!',
                    text: 'Tindakan ini akan MENGHAPUS TERLEBIH DAHULU semua data biodata mahasiswa di database lokal, lalu menarik data baru yang valid dari Feeder. Apakah Anda yakin?',
                    icon: 'warning',
                    input: 'select',
                    inputOptions: prodiOptions,
                    inputValue: 'all',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Bersihkan & Sinkron!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#fd625e',
                    cancelButtonColor: '#74788d',
                    showLoaderOnConfirm: true,
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Anda harus memilih Program Studi!'
                        }
                    },
                    preConfirm: (prodiId) => {
                        return $.ajax({
                            url: "{{ route('biodata-mahasiswa.sync') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id_prodi: prodiId,
                                clear: 'true'
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
                            title: 'Berhasil Bersihkan & Sinkron!',
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
