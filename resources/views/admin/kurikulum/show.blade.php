@extends('templates.layout')

@section('title', 'Detail Kurikulum')
@section('page-title', 'Detail Kurikulum')

@push('css')
    <!-- SweetAlert2 -->
    <link href="{{ asset('templates/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .semester-group {
            background-color: #f8f9fa;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-left: 5px solid #5156be;
            border-radius: 4px;
        }

        .semester-title {
            margin-bottom: 0;
            font-weight: 600;
            color: #495057;
        }

        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title">{{ $kurikulum['nama_kurikulum'] }}</h4>
                        <p class="text-muted mb-0">{{ $kurikulum['nama_program_studi'] }} ({{ $kurikulum['id_semester'] }})
                        </p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                            data-bs-target="#copyModal">
                            <i class="fas fa-copy"></i> Salin dari Kurikulum Lain
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#bulkAddModal">
                            <i class="fas fa-plus"></i> Tambah Matakuliah
                        </button>
                        <a href="{{ route('kurikulum.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h5 class="mb-1 text-primary">{{ $kurikulum['jumlah_sks_lulus'] }}</h5>
                                <span class="text-muted font-size-13">SKS Lulus</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h5 class="mb-1 text-success">{{ $kurikulum['jumlah_sks_wajib'] }}</h5>
                                <span class="text-muted font-size-13">SKS Wajib</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h5 class="mb-1 text-info">{{ $kurikulum['jumlah_sks_pilihan'] }}</h5>
                                <span class="text-muted font-size-13">SKS Pilihan</span>
                            </div>
                        </div>
                    </div>

                    @forelse ($groupedMatkul as $semester => $items)
                        <div class="semester-group">
                            <h5 class="semester-title">Semester {{ $semester }}</h5>
                        </div>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th width="120">Kode MK</th>
                                        <th>Nama Mata Kuliah</th>
                                        <th width="80">SKS</th>
                                        <th width="100">Wajib?</th>
                                        <th width="100">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item['kode_mata_kuliah'] }}</td>
                                            <td>{{ $item['nama_mata_kuliah'] }}</td>
                                            <td>{{ $item['sks_mata_kuliah'] }}</td>
                                            <td>
                                                @if ($item['apakah_wajib'] == '1')
                                                    <span class="badge bg-primary">Wajib</span>
                                                @else
                                                    <span class="badge bg-secondary">Pilihan</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-danger btn-delete-matkul"
                                                    data-id-kurikulum="{{ $kurikulum['id_kurikulum'] }}"
                                                    data-id-matkul="{{ $item['id_matkul'] }}"
                                                    data-nama="{{ $item['nama_mata_kuliah'] }}">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @empty
                        <div class="alert alert-info">Tidak ada mata kuliah yang terdaftar dalam kurikulum ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Add Modal -->
    <div class="modal fade" id="bulkAddModal" tabindex="-1" aria-labelledby="bulkAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="bulkAddForm">
                    @csrf
                    <input type="hidden" name="id_kurikulum" value="{{ $kurikulum['id_kurikulum'] }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkAddModalLabel">Tambah Matakuliah ke Kurikulum</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Semester</label>
                                <input type="number" name="semester" class="form-control" placeholder="Contoh: 1" required>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="bulkMatkulTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mata Kuliah</th>
                                        <th width="100">SKS Total</th>
                                        <th width="100">Wajib?</th>
                                        <th width="50">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows added dynamically -->
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddRow">
                            <i class="fas fa-plus"></i> Tambah Baris
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload"></i> Simpan ke Feeder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Copy Modal -->
    <div class="modal fade" id="copyModal" tabindex="-1" aria-labelledby="copyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="copyForm">
                    @csrf
                    <input type="hidden" name="id_kurikulum_target" value="{{ $kurikulum['id_kurikulum'] }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="copyModalLabel">Salin dari Kurikulum Lain</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Kurikulum Sumber</label>
                            <select name="id_kurikulum_source" id="sourceKurikulumSelect" class="form-control" required>
                                <option value="">Cari Kurikulum...</option>
                            </select>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Semua mata kuliah dari kurikulum sumber akan
                            disalin ke kurikulum ini dengan semester yang sama.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info">Mulai Salin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- SweetAlert2 -->
    <script src="{{ asset('templates/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Select2 for Source Kurikulum
            $('#sourceKurikulumSelect').select2({
                dropdownParent: $('#copyModal'),
                ajax: {
                    url: "{{ route('kurikulum.ajax.list') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.nama_kurikulum + ' (' + item.nama_program_studi +
                                        ')',
                                    id: item.id_kurikulum
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#copyForm').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                Swal.fire({
                    title: 'Peringatan',
                    text: 'Apakah Anda yakin ingin menyalin mata kuliah dari kurikulum lain? Ini mungkin memerlukan waktu beberapa saat.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Salin!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3085d6',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Sedang Menyalin...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });

                        $.ajax({
                            url: "{{ route('kurikulum.matkul.copy') }}",
                            type: "POST",
                            data: formData,
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil!', response.message, 'success')
                                        .then(() => {
                                            location.reload();
                                        });
                                } else {
                                    Swal.fire('Gagal!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON.message ||
                                    'Terjadi kesalahan sistem', 'error');
                            }
                        });
                    }
                });
            });

            let rowIndex = 0;

            // Add initial row
            addRow();

            $('#btnAddRow').click(function() {
                addRow();
            });

            function addRow() {
                let row = `
                <tr id="row-${rowIndex}">
                    <td>
                        <select name="matkul[${rowIndex}][id_matkul]" class="form-control matkul-select" required>
                            <option value="">Cari Mata Kuliah...</option>
                        </select>
                        <div class="row mt-2 sks-inputs" style="display:none;">
                            <div class="col-md-2">
                                <small class="text-muted">TM</small>
                                <input type="number" step="0.5" name="matkul[${rowIndex}][sks_tatap_muka]" class="form-control form-control-sm" value="0">
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted">Prak</small>
                                <input type="number" step="0.5" name="matkul[${rowIndex}][sks_praktek]" class="form-control form-control-sm" value="0">
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">LP</small>
                                <input type="number" step="0.5" name="matkul[${rowIndex}][sks_praktek_lapangan]" class="form-control form-control-sm" value="0">
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted">Sim</small>
                                <input type="number" step="0.5" name="matkul[${rowIndex}][sks_simulasi]" class="form-control form-control-sm" value="0">
                            </div>
                        </div>
                    </td>
                    <td>
                        <input type="number" name="matkul[${rowIndex}][sks_mata_kuliah]" class="form-control sks-total" value="0" readonly>
                    </td>
                    <td>
                        <div class="form-check">
                            <input type="checkbox" name="matkul[${rowIndex}][apakah_wajib]" class="form-check-input" value="1" checked>
                            <label class="form-check-label">Wajib</label>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm btnRemoveRow"><i class="fas fa-times"></i></button>
                    </td>
                </tr>`;

                $('#bulkMatkulTable tbody').append(row);
                initSelect2(rowIndex);
                rowIndex++;
            }

            $(document).on('input', '.sks-inputs input', function() {
                let parent = $(this).closest('tr');
                let tm = parseFloat(parent.find('input[name*="sks_tatap_muka"]').val()) || 0;
                let prak = parseFloat(parent.find('input[name*="sks_praktek"]').val()) || 0;
                let lp = parseFloat(parent.find('input[name*="sks_praktek_lapangan"]').val()) || 0;
                let sim = parseFloat(parent.find('input[name*="sks_simulasi"]').val()) || 0;
                parent.find('.sks-total').val(tm + prak + lp + sim);
            });

            $(document).on('click', '.btnRemoveRow', function() {
                $(this).closest('tr').remove();
            });

            function initSelect2(idx) {
                $(`select[name="matkul[${idx}][id_matkul]"]`).select2({
                    dropdownParent: $('#bulkAddModal'),
                    ajax: {
                        url: "{{ route('kurikulum.ajax.matkul-list') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.kode_mata_kuliah + ' - ' + item
                                            .nama_mata_kuliah,
                                        id: item.id_matkul,
                                        sks: item.sks_mata_kuliah,
                                        tm: item.sks_tatap_muka,
                                        prak: item.sks_praktek,
                                        lp: item.sks_praktek_lapangan,
                                        sim: item.sks_simulasi
                                    }
                                })
                            };
                        },
                        cache: true
                    }
                }).on('select2:select', function(e) {
                    let data = e.params.data;
                    let parent = $(this).closest('tr');
                    parent.find('.sks-total').val(data.sks);
                    parent.find('input[name*="sks_tatap_muka"]').val(data.tm);
                    parent.find('input[name*="sks_praktek"]').val(data.prak);
                    parent.find('input[name*="sks_praktek_lapangan"]').val(data.lp);
                    parent.find('input[name*="sks_simulasi"]').val(data.sim);
                    parent.find('.sks-inputs').show();
                });
            }

            $('#bulkAddForm').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                Swal.fire({
                    title: 'Sedang Mengirim Data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                $.ajax({
                    url: "{{ route('kurikulum.matkul.store-bulk') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Terjadi Kesalahan', response.message + (response.errors ?
                                '<br><small>' + response.errors.join('<br>') +
                                '</small>' : ''), 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message ||
                            'Terjadi kesalahan sistem', 'error');
                    }
                });
            });

            $('.btn-delete-matkul').on('click', function() {
                var id_kurikulum = $(this).data('id-kurikulum');
                var id_matkul = $(this).data('id-matkul');
                var nama = $(this).data('nama');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Hapus " + nama + " dari kurikulum ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#fd625e',
                    cancelButtonColor: '#5156be',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            url: "{{ route('kurikulum.matkul.destroy') }}",
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id_kurikulum: id_kurikulum,
                                id_matkul: id_matkul
                            }
                        }).then(response => {
                            if (!response.success) {
                                throw new Error(response.message ||
                                    'Gagal menghapus data');
                            }
                            return response;
                        }).catch(error => {
                            Swal.showValidationMessage(
                                `Request failed: ${error.message || 'Terjadi kesalahan'}`
                            );
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire('Berhasil!', result.value.message, 'success').then(() => {
                            location.reload();
                        });
                    }
                });
            });
        });
    </script>
@endpush
