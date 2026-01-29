@extends('templates.layout')

@section('title', 'Daftar Mahasiswa')
@section('page-title', 'Daftar Mahasiswa')

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
                    <h4 class="card-title">Daftar Registrasi Mahasiswa (Enrollment)</h4>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#filterModal">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <button id="btn-sync" class="btn btn-info btn-sm">
                            <i class="fas fa-sync-alt"></i> Sinkronkan Data
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive nowrap w-100" id="mahasiswa-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Program Studi</th>
                                    <th>Periode Masuk</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Status</th>
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
                    <h5 class="modal-title" id="filterModalLabel"><i class="fas fa-filter me-2"></i>Filter Data Mahasiswa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Angkatan</label>
                                <select class="form-control select2-multiple" name="id_periodes[]" multiple="multiple">
                                    @foreach ($periodes as $p)
                                        <option value="{{ $p->id_tahun_ajaran }}">{{ $p->nama_tahun_ajaran }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Bisa pilih lebih dari satu angkatan</small>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Program Studi</label>
                                <select class="form-control select2-multiple" name="prodi_names[]" multiple="multiple">
                                    @foreach ($prodis as $pr)
                                        <option value="{{ $pr->nama_program_studi }}">{{ $pr->nama_program_studi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Status Mahasiswa</label>
                                <select class="form-control select2-multiple" name="status_names[]" multiple="multiple">
                                    @foreach ($statuses as $s)
                                        <option value="{{ $s->nama_status_mahasiswa }}">{{ $s->nama_status_mahasiswa }}
                                        </option>
                                    @endforeach
                                </select>
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
                    url: "{{ route('mahasiswa.index') }}",
                    data: function(d) {
                        d.prodi_names = $('select[name="prodi_names[]"]').val();
                        d.id_periodes = $('select[name="id_periodes[]"]').val();
                        d.status_names = $('select[name="status_names[]"]').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nim',
                        name: 'nim',
                        render: function(data, type, row) {
                            if (!row.id_registrasi_mahasiswa || !data) {
                                return `<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Data Tidak Lengkap</span>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'nama_mahasiswa',
                        name: 'nama_mahasiswa'
                    },
                    {
                        data: 'nama_program_studi',
                        name: 'nama_program_studi'
                    },
                    {
                        data: 'nama_periode_masuk',
                        name: 'nama_periode_masuk'
                    },
                    {
                        data: 'tanggal_lahir',
                        name: 'tanggal_lahir'
                    },
                    {
                        data: 'nama_status_mahasiswa',
                        name: 'nama_status_mahasiswa',
                        render: function(data) {
                            let badgeClass = data === 'AKTIF' ? 'bg-success' : 'bg-secondary';
                            return `<span class="badge ${badgeClass}">${data || '-'}</span>`;
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

            // Apply Filter
            $('#btn-apply-filter').click(function() {
                table.ajax.reload();
                $('#filterModal').modal('hide');
            });

            // Reset Filter
            $('#btn-reset-filter').click(function() {
                $('#filterForm')[0].reset();
                $('.select2-multiple').val(null).trigger('change');
                table.ajax.reload();
                $('#filterModal').modal('hide');
            });

            $('#btn-sync').on('click', function() {
                Swal.fire({
                    title: 'Sinkronisasi Data',
                    text: 'Menyinkronkan daftar registrasi mahasiswa dengan NeoFeeder?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Sinkronkan!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            url: "{{ route('mahasiswa.sync') }}",
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
