@extends('templates.layout')

@section('title', 'Daftar Mata Kuliah Lokal')
@section('page-title', 'Mata Kuliah Lokal')

@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Mata Kuliah Lokal</h4>
                    <div>
                        <button class="btn btn-primary btn-sm">Tambah Mata Kuliah</button>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="mdi mdi-upload"></i> Import CSV
                        </button>
                        <button id="bulkSyncBtn" class="btn btn-info btn-sm" style="display:none;"
                            onclick="showBulkSyncModal()">
                            <i class="mdi mdi-refresh"></i> Bulk Sync
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th width="20px"><input type="checkbox" id="checkAll"></th>
                                    <th>No</th>
                                    <th>Kode MK</th>
                                    <th>Nama Mata Kuliah</th>
                                    <th>SKS</th>
                                    <th>Program Studi</th>
                                    <th>Tatap Muka</th>
                                    <th>Praktek</th>
                                    <th>Lapangan</th>
                                    <th>Simulasi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($matakuliah as $item)
                                    <tr>
                                        <td><input type="checkbox" class="matkul-checkbox" value="{{ $item->id }}"></td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->kode_mata_kuliah }}</td>
                                        <td>{{ $item->nama_mata_kuliah }}</td>
                                        <td>{{ $item->sks_mata_kuliah }}</td>
                                        <td>{{ $item->nama_program_studi }}</td>
                                        <td>{{ $item->sks_tatap_muka }}</td>
                                        <td>{{ $item->sks_praktek }}</td>
                                        <td>{{ $item->sks_praktek_lapangan }}</td>
                                        <td>{{ $item->sks_simulasi }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $item->status == 'sudah sync' ? 'bg-success' : 'bg-warning' }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="syncMatkul('{{ $item->id }}')"
                                                title="Sync ke Feeder"><i class="mdi mdi-refresh"></i></button>
                                            <button class="btn btn-sm btn-warning"
                                                onclick="editMatkul('{{ $item->id }}')" title="Edit"><i
                                                    class="mdi mdi-pencil"></i></button>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="deleteMatkul('{{ $item->id }}')" title="Hapus"><i
                                                    class="mdi mdi-trash-can"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">Data masih kosong.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('matakuliah-lokal.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Mata Kuliah Lokal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="id_prodi" class="form-label">Program Studi</label>
                            <select name="id_prodi" id="id_prodi" class="form-select" required>
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id_prodi }}">{{ $prodi->nama_program_studi }}
                                        ({{ $prodi->nama_jenjang_pendidikan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="file_csv" class="form-label">File CSV</label>
                            <input type="file" name="file_csv" id="file_csv" class="form-control" accept=".csv"
                                required>
                            <small class="text-muted">Gunakan format pemisah titik koma (;) sesuai dengan file `matakuliah
                                ES.csv`</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Mulai Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Mata Kuliah Lokal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_kode_mata_kuliah" class="form-label">Kode Mata Kuliah</label>
                            <input type="text" class="form-control" id="edit_kode_mata_kuliah"
                                name="kode_mata_kuliah" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nama_mata_kuliah" class="form-label">Nama Mata Kuliah</label>
                            <input type="text" class="form-control" id="edit_nama_mata_kuliah"
                                name="nama_mata_kuliah" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_sks_mata_kuliah" class="form-label">SKS Total</label>
                                <input type="number" step="0.01" class="form-control" id="edit_sks_mata_kuliah"
                                    name="sks_mata_kuliah" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_sks_tatap_muka" class="form-label">SKS Tatap Muka</label>
                                <input type="number" step="0.01" class="form-control" id="edit_sks_tatap_muka"
                                    name="sks_tatap_muka" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="edit_sks_praktek" class="form-label">Praktek</label>
                                <input type="number" step="0.01" class="form-control" id="edit_sks_praktek"
                                    name="sks_praktek" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_sks_praktek_lapangan" class="form-label">Lapangan</label>
                                <input type="number" step="0.01" class="form-control" id="edit_sks_praktek_lapangan"
                                    name="sks_praktek_lapangan" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_sks_simulasi" class="form-label">Simulasi</label>
                                <input type="number" step="0.01" class="form-control" id="edit_sks_simulasi"
                                    name="sks_simulasi" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Sync Modal -->
    <div class="modal fade" id="syncModal" tabindex="-1" aria-labelledby="syncModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="syncForm">
                    @csrf
                    <input type="hidden" id="sync_id">
                    <input type="hidden" id="is_bulk" value="0">
                    <div class="modal-header">
                        <h5 class="modal-title" id="syncModalLabel">Sinkronisasi Mata Kuliah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <p class="mb-0" id="sync_info_text">Anda akan mengirim data <strong><span
                                        id="sync_nama_matkul"></span></strong>
                                ke Feeder.</p>
                        </div>
                        <div class="mb-3">
                            <label for="sync_id_jenis_mata_kuliah" class="form-label">Jenis Mata Kuliah</label>
                            <select name="id_jenis_mata_kuliah" id="sync_id_jenis_mata_kuliah" class="form-select">
                                <option value="">-- Pilih Jenis Mata Kuliah --</option>
                                <option value="A">Wajib</option>
                                <option value="B">Pilihan</option>
                                <option value="C">Wajib Peminatan</option>
                                <option value="D">Pilihan Peminatan</option>
                                <option value="S">Tugas Akhir / Skripsi / Tesis / Disertasi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="sync_id_kelompok_mata_kuliah" class="form-label">Kelompok Mata Kuliah</label>
                            <select name="id_kelompok_mata_kuliah" id="sync_id_kelompok_mata_kuliah" class="form-select">
                                <option value="">-- Pilih Kelompok Mata Kuliah --</option>
                                <option value="A">MPK</option>
                                <option value="B">MKK</option>
                                <option value="C">MKB</option>
                                <option value="D">MPB</option>
                                <option value="E">MBB</option>
                                <option value="F">MKU / MKDU</option>
                                <option value="G">MKDK</option>
                                <option value="H">MKK</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Kirim ke Server</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Check All functionality
        $('#checkAll').click(function() {
            $('.matkul-checkbox').prop('checked', this.checked);
            toggleBulkBtn();
        });

        $('.matkul-checkbox').click(function() {
            toggleBulkBtn();
        });

        function toggleBulkBtn() {
            if ($('.matkul-checkbox:checked').length > 0) {
                $('#bulkSyncBtn').show();
            } else {
                $('#bulkSyncBtn').hide();
                $('#checkAll').prop('checked', false);
            }
        }

        function showBulkSyncModal() {
            let count = $('.matkul-checkbox:checked').length;
            $('#is_bulk').val('1');
            $('#sync_info_text').html(
                `Terdapat <strong>${count}</strong> data terpilih yang akan disinkronisasi dengan kategori yang sama.`);
            $('#syncModal').modal('show');
        }

        function editMatkul(id) {
            $.get("{{ url('admin/matakuliah-lokal') }}/" + id, function(data) {
                $('#edit_id').val(data.id);
                $('#edit_kode_mata_kuliah').val(data.kode_mata_kuliah);
                $('#edit_nama_mata_kuliah').val(data.nama_mata_kuliah);
                $('#edit_sks_mata_kuliah').val(data.sks_mata_kuliah);
                $('#edit_sks_tatap_muka').val(data.sks_tatap_muka);
                $('#edit_sks_praktek').val(data.sks_praktek);
                $('#edit_sks_praktek_lapangan').val(data.sks_praktek_lapangan);
                $('#edit_sks_simulasi').val(data.sks_simulasi);
                $('#editModal').modal('show');
            });
        }

        $('#editForm').submit(function(e) {
            e.preventDefault();
            let id = $('#edit_id').val();
            let formData = $(this).serialize();

            $.ajax({
                url: "{{ url('admin/matakuliah-lokal') }}/" + id,
                type: "POST", // Use POST for PUT method spoofing
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#editModal').modal('hide');
                        Swal.fire('Berhasil', response.message, 'success').then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMsg = "";
                    $.each(errors, function(key, value) {
                        errorMsg += value + "<br>";
                    });
                    Swal.fire('Error', errorMsg, 'error');
                }
            });
        });

        function deleteMatkul(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/matakuliah-lokal') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Dihapus!', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON.message, 'error');
                        }
                    });
                }
            });
        }

        function syncMatkul(id) {
            $.get("{{ url('admin/matakuliah-lokal') }}/" + id, function(data) {
                $('#is_bulk').val('0');
                $('#sync_id').val(data.id);
                $('#sync_info_text').html(
                    `Anda akan mengirim data <strong>${data.nama_mata_kuliah}</strong> ke Feeder.`);
                $('#syncModal').modal('show');
            });
        }

        $('#syncForm').submit(function(e) {
            e.preventDefault();
            let isBulk = $('#is_bulk').val() == '1';
            let url = isBulk ? "{{ route('matakuliah-lokal.bulk-sync') }}" :
                "{{ url('admin/matakuliah-lokal') }}/" + $('#sync_id').val() + "/sync";

            let formData = $(this).serializeArray();
            if (isBulk) {
                $('.matkul-checkbox:checked').each(function() {
                    formData.push({
                        name: 'ids[]',
                        value: $(this).val()
                    });
                });
            }

            Swal.fire({
                title: 'Sedang Sinkron...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            $.ajax({
                url: url,
                type: "POST",
                data: $.param(formData),
                success: function(response) {
                    $('#syncModal').modal('hide');
                    if (response.success) {
                        let msg = response.message;
                        if (response.errors && response.errors.length > 0) {
                            msg += "<br><small class='text-danger'>" + response.errors.join("<br>") +
                                "</small>";
                        }
                        Swal.fire('Hasil Sinkronisasi', msg, 'info').then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON.message || 'Terjadi kesalahan sistem', 'error');
                }
            });
        });
    </script>
@endpush
