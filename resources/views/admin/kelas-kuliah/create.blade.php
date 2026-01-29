@extends('templates.layout')

@section('title', 'Generate Kelas Kuliah')
@section('page-title', 'Generate Kelas Kuliah')

@section('content')
    <form id="form-generate" action="{{ route('kelas-kuliah.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-xl-7">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Form Generate Kelas Kuliah</h4>
                        <p class="card-title-desc">Silakan isi form di bawah ini untuk membuat kelas kuliah baru di Neo
                            Feeder.
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="id_prodi" class="form-label">Program Studi</label>
                            <select class="form-select" name="id_prodi" id="id_prodi" required>
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id_prodi }}">{{ $prodi->nama_program_studi }}
                                        ({{ $prodi->kode_program_studi }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_kurikulum" class="form-label">Kurikulum</label>
                            <select class="form-select" name="id_kurikulum" id="id_kurikulum" required disabled>
                                <option value="">-- Pilih Program Studi Terlebih Dahulu --</option>
                            </select>
                            <div id="loading-kurikulum" class="mt-1 d-none">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                                <small class="text-primary ms-1">Mengambil data kurikulum...</small>
                            </div>
                        </div>

                        <div id="kurikulum-card" class="card bg-light border d-none mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-primary"><i class="fas fa-info-circle me-1"></i> Detail Kurikulum
                                </h6>
                                <hr class="mt-1 mb-2">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p class="mb-1"><small class="text-muted">Nama Kurikulum:</small><br><strong
                                                id="kur-nama">-</strong></p>
                                        <p class="mb-1"><small class="text-muted">Prodi:</small><br><strong
                                                id="kur-prodi">-</strong></p>
                                        <p class="mb-1"><small class="text-muted">Semester Mulai:</small><br><strong
                                                id="kur-semester">-</strong></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="mb-1"><small class="text-muted">SKS Lulus:</small><br><strong
                                                id="kur-sks-lulus">-</strong></p>
                                        <p class="mb-1"><small class="text-muted">SKS Wajib:</small><br><strong
                                                id="kur-sks-wajib">-</strong></p>
                                        <p class="mb-1"><small class="text-muted">SKS Pilihan:</small><br><strong
                                                id="kur-sks-pilihan">-</strong></p>
                                    </div>
                                </div>
                                <div class="mt-3 text-end">
                                    <button type="button" id="btn-view-matkul-kur" class="btn btn-soft-primary btn-sm">
                                        <i class="fas fa-list me-1"></i> Lihat Matakuliah Kurikulum
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="id_semester" class="form-label">Semester (Aktif)</label>
                            <select class="form-select" name="id_semester" id="id_semester" required>
                                <option value="">-- Pilih Semester --</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id_semester }}">{{ $semester->nama_semester }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="nama_kelas_kuliah" class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" name="nama_kelas_kuliah" id="nama_kelas_kuliah"
                                placeholder="Contoh: A, B, atau Reguler" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_mulai_efektif" class="form-label">Tanggal Mulai Efektif</label>
                                    <input type="date" class="form-control" name="tanggal_mulai_efektif"
                                        id="tanggal_mulai_efektif" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_akhir_efektif" class="form-label">Tanggal Akhir Efektif</label>
                                    <input type="date" class="form-control" name="tanggal_akhir_efektif"
                                        id="tanggal_akhir_efektif" required>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <a href="{{ route('kelas-kuliah.index') }}" class="btn btn-outline-secondary w-md">Kembali</a>
                            <button type="submit" id="btn-generate" class="btn btn-primary w-md">
                                <span class="spinner-border spinner-border-sm d-none me-1" role="status"
                                    aria-hidden="true"></span>
                                Generate Kelas
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5 col-md-5">
                <div class="card border border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title text-white mb-0">Daftar Mata Kuliah Kurikulum</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="loading-matkul-kur" class="text-center p-4 d-none">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 mb-0">Memuat mata kuliah...</p>
                        </div>
                        <div id="matkul-kur-empty" class="text-center p-4">
                            <i class="fas fa-book-open fa-3x text-light mb-3"></i>
                            <p class="text-muted">Pilih prodi & kurikulum terlebih dahulu, lalu klik "Lihat Matakuliah
                                Kurikulum"
                            </p>
                        </div>
                        <div id="matkul-kur-list" class="p-3 d-none" style="max-height: 600px; overflow-y: auto;">
                            <!-- Mata kuliah per semester will be rendered here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            let curriculaData = [];

            $('#id_prodi').on('change', function() {
                const idProdi = $(this).val();
                const $kurSelect = $('#id_kurikulum');
                const $loadingKur = $('#loading-kurikulum');
                const $kurCard = $('#kurikulum-card');

                // Reset selects and card
                $kurSelect.html('<option value="">-- Pilih Kurikulum --</option>').attr('disabled', true);
                $kurCard.addClass('d-none');
                curriculaData = [];

                // Reset right column
                $('#matkul-kur-list').addClass('d-none').empty();
                $('#matkul-kur-empty').removeClass('d-none');

                if (!idProdi) {
                    $kurSelect.html('<option value="">-- Pilih Program Studi Terlebih Dahulu --</option>');
                    return;
                }

                $loadingKur.removeClass('d-none');

                // Fetch Kurikulum List
                $.ajax({
                    url: "{{ route('kelas-kuliah.ajax.kurikulum-detail') }}",
                    data: {
                        id_prodi: idProdi
                    },
                    success: function(response) {
                        if (response.data && response.data.length > 0) {
                            curriculaData = response.data;
                            response.data.forEach(function(item) {
                                $kurSelect.append(
                                    `<option value="${item.id_kurikulum}">${item.nama_kurikulum} (${item.semester_mulai_berlaku})</option>`
                                );
                            });
                            $kurSelect.attr('disabled', false);

                            // Auto-select if only one
                            if (response.data.length === 1) {
                                $kurSelect.val(response.data[0].id_kurikulum).trigger('change');
                            }
                        } else {
                            $kurSelect.html(
                                '<option value="">-- Tidak ada data kurikulum --</option>');
                        }
                    },
                    error: function() {
                        alert('Gagal mengambil data kurikulum.');
                    },
                    complete: function() {
                        $loadingKur.addClass('d-none');
                    }
                });
            });

            // Handle Kurikulum Change to show details
            $('#id_kurikulum').on('change', function() {
                const idKur = $(this).val();
                const $kurCard = $('#kurikulum-card');

                if (!idKur) {
                    $kurCard.addClass('d-none');
                    return;
                }

                const kur = curriculaData.find(k => k.id_kurikulum === idKur);
                if (kur) {
                    $('#kur-nama').text(kur.nama_kurikulum);
                    $('#kur-prodi').text(kur.nama_program_studi);
                    $('#kur-semester').text(kur.semester_mulai_berlaku);
                    $('#kur-sks-lulus').text(kur.jumlah_sks_lulus);
                    $('#kur-sks-wajib').text(kur.jumlah_sks_wajib);
                    $('#kur-sks-pilihan').text(kur.jumlah_sks_pilihan);
                    $kurCard.removeClass('d-none');
                }

                // Reset right column when curriculum changes
                $('#matkul-kur-list').addClass('d-none').empty();
                $('#matkul-kur-empty').removeClass('d-none');
            });

            // Handle View Matkul Kurikulum Button
            $('#btn-view-matkul-kur').on('click', function() {
                const idKur = $('#id_kurikulum').val();
                if (!idKur) return;

                const $loading = $('#loading-matkul-kur');
                const $empty = $('#matkul-kur-empty');
                const $list = $('#matkul-kur-list');

                $empty.addClass('d-none');
                $list.addClass('d-none').empty();
                $loading.removeClass('d-none');

                $.ajax({
                    url: "{{ route('kelas-kuliah.ajax.matkul-kurikulum') }}",
                    data: {
                        id_kurikulum: idKur
                    },
                    success: function(response) {
                        if (response.data && response.data.length > 0) {
                            // Group by semester
                            const grouped = {};
                            response.data.forEach(item => {
                                if (!grouped[item.semester]) grouped[item
                                    .semester] = [];
                                grouped[item.semester].push(item);
                            });

                            // Sort semesters and render
                            Object.keys(grouped).sort((a, b) => a - b).forEach(semester => {
                                let html = `<div class="mb-3">
                                    <h6 class="bg-light p-2 border-start border-primary border-3 mb-2">Semester ${semester}</h6>
                                    <div class="list-group list-group-flush">`;

                                grouped[semester].forEach(item => {
                                    html += `
                                        <div class="list-group-item px-2 py-1">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="selected_matkul[]" value="${item.id_matkul}" id="matkul-${item.id_matkul}">
                                                <label class="form-check-label d-block" for="matkul-${item.id_matkul}">
                                                    <span class="fw-medium">${item.kode_mata_kuliah}</span><br>
                                                    <span class="text-muted small">${item.nama_mata_kuliah} (${item.sks_mata_kuliah} SKS)</span>
                                                </label>
                                            </div>
                                        </div>
                                    `;
                                });

                                html += `</div></div>`;
                                $list.append(html);
                            });

                            $list.removeClass('d-none');
                        } else {
                            $empty.removeClass('d-none').find('p').text(
                                'Tidak ada mata kuliah ditemukan untuk kurikulum ini.');
                        }
                    },
                    error: function() {
                        alert('Gagal mengambil data mata kuliah kurikulum.');
                        $empty.removeClass('d-none');
                    },
                    complete: function() {
                        $loading.addClass('d-none');
                    }
                });
            });

            // Handle AJAX Form Submission
            $('#form-generate').on('submit', function(e) {
                e.preventDefault();

                const $btn = $('#btn-generate');
                const $spinner = $btn.find('.spinner-border');
                const formData = $(this).serialize();

                $btn.attr('disabled', true);
                $spinner.removeClass('d-none');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            let msg = response.message;
                            if (response.errors && response.errors.length > 0) {
                                msg += '\nNamun terdapat beberapa error: ' + response.errors
                                    .join(', ');
                            }

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: msg,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('kelas-kuliah.index') }}";
                                });
                            } else {
                                alert(msg);
                                window.location.href = "{{ route('kelas-kuliah.index') }}";
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: response.message ||
                                        'Terjadi kesalahan saat proses generate.'
                                });
                            } else {
                                alert(response.message ||
                                    'Terjadi kesalahan saat proses generate.');
                            }
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Terjadi kesalahan sistem.';
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            errorMsg = Object.values(errors).flat().join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMsg = xhr.responseJSON.errors.join('\n');
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMsg
                            });
                        } else {
                            alert(errorMsg);
                        }
                    },
                    complete: function() {
                        $btn.attr('disabled', false);
                        $spinner.addClass('d-none');
                    }
                });
            });
        });
    </script>
@endpush
