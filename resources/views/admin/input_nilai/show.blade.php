@extends('templates.layout')

@section('title', 'KRS & Input Nilai')
@section('page-title', 'KRS & Input Nilai Kelas')

@section('content')
    <div class="row">
        <!-- Informasi Kelas -->
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Informasi Kelas Kuliah</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <p class="mb-1 text-muted">Program Studi</p>
                            <h6 class="font-size-14">{{ $detailKelas['nama_program_studi'] ?? '-' }}</h6>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1 text-muted">Mata Kuliah</p>
                            <h6 class="font-size-14">{{ $detailKelas['nama_mata_kuliah'] ?? '-' }}</h6>
                        </div>
                        <div class="col-md-2">
                            <p class="mb-1 text-muted">Nama Kelas</p>
                            <h6 class="font-size-14">{{ $detailKelas['nama_kelas_kuliah'] ?? '-' }}</h6>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted">Dosen Pengajar</p>
                            <h6 class="font-size-14">{{ $detailKelas['nama_dosen'] ?? '-' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Tabel Peserta Kelas & Input Nilai -->
        <div class="col-lg-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-white mb-0">Daftar Mahasiswa & Input Nilai</h4>
                    <div>
                        <button class="btn btn-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalKRS">
                            <i class="bx bx-user-plus"></i> Tambah Mahasiswa
                        </button>
                        <button class="btn btn-light btn-sm" id="btnRefreshPeserta" onclick="loadPesertaKelas()">
                            <i class="bx bx-refresh"></i> Refresh Data
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <i class="bx bx-info-circle"></i> <strong>Perhatian:</strong> Komponen nilai (Kuis, Tugas, UTS, UAS)
                        hanya berfungsi sebagai kalkulator sementara di UI ini dan tidak tersimpan di database. Pastikan
                        Anda klik <strong>Simpan Nilai Semua Mahasiswa</strong> agar <strong>Nilai Akhir</strong> masuk ke
                        Neofeeder sebelum meninggalkan halaman ini.
                    </div>

                    <div class="table-responsive">
                        <form id="formInputNilai">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th style="width: 15%">NIM / Nama</th>
                                        <th style="width: 10%">Kuis<br><small class="text-muted">(10%)</small></th>
                                        <th style="width: 10%">Tugas 1<br><small class="text-muted">(10%)</small></th>
                                        <th style="width: 10%">Tugas 2<br><small class="text-muted">(10%)</small></th>
                                        <th style="width: 10%">UTS<br><small class="text-muted">(30%)</small></th>
                                        <th style="width: 10%">UAS<br><small class="text-muted">(40%)</small></th>
                                        <th style="width: 10%" class="bg-info bg-soft">Nilai Angka<br>(Auto)</th>
                                        <th style="width: 8%" class="bg-info bg-soft">Huruf</th>
                                        <th style="width: 8%" class="bg-info bg-soft">Indeks</th>
                                        <th style="width: 5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyPesertaKelas">
                                    <tr>
                                        <td colspan="11" class="text-center py-4">Memuat data peserta kelas...</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="text-end mt-3">
                                <button type="button" class="btn btn-primary" id="btnSimpanNilai">
                                    <i class="bx bx-save"></i> Simpan Nilai Semua Mahasiswa
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal KRS Kolektif -->
    <div class="modal fade" id="modalKRS" tabindex="-1" aria-labelledby="modalKRSLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalKRSLabel">Tambah Peserta Kelas (KRS Kolektif)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formCariMahasiswa">
                        <div class="row align-items-end">
                            <div class="col-md-3 mb-3">
                                <label for="filterAngkatan" class="form-label">Angkatan / Thn Masuk</label>
                                <select class="form-select" id="filterAngkatan" required>
                                    <option value="">-- Pilih Tahun Masuk --</option>
                                    @foreach($angkatanList as $angkatan)
                                        <option value="{{ $angkatan }}">{{ $angkatan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="filterNama" class="form-label">Cari Nama (Opsional)</label>
                                <input type="text" class="form-control" id="filterNama"
                                    placeholder="Ketik nama mahasiswa...">
                            </div>
                            <div class="col-md-3 mb-3">
                                <button type="submit" class="btn btn-secondary w-md" id="btnCari">
                                    <span class="spinner-border spinner-border-sm d-none" id="spinnerCari"></span>
                                    <i class="bx bx-search-alt"></i> Cari
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id="hasilPencarianContainer" class="d-none mt-3">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%" class="text-center">
                                            <input class="form-check-input" type="checkbox" id="checkAllMhs">
                                        </th>
                                        <th>NIM</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>Angkatan</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyHasilPencarian"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success" id="btnSimpanPeserta">
                        <i class="bx bx-plus-circle"></i> Tambahkan ke Kelas
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Skala Nilai (Hidden) for JS calculation -->
    <script>
        window.skalaNilai = @json($skalaNilai);
        window.idKelas = '{{ $id_kelas }}';
        window.idProdi = '{{ $detailKelas['id_prodi'] ?? '' }}';
    </script>
@endsection

@push('js')
    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Setup CSRF for all AJAX calls
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // 1. Fungsi Pencarian Mahasiswa
        $('#formCariMahasiswa').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#btnCari');
            const spinner = $('#spinnerCari');

            btn.prop('disabled', true);
            spinner.removeClass('d-none');

            $.ajax({
                url: "{{ route('input-nilai.ajax.cari-mahasiswa') }}",
                method: "POST",
                data: {
                    id_periode: $('#filterAngkatan').val(),
                    nama: $('#filterNama').val(),
                    id_prodi: window.idProdi
                },
                success: function(res) {
                    if (res.success) {
                        let rows = '';
                        if (res.data.length === 0) {
                            rows =
                                '<tr><td colspan="4" class="text-center text-muted">Tidak ditemukan mahasiswa pada angkatan/pencarian ini.</td></tr>';
                        } else {
                            res.data.forEach(function(mhs) {
                                rows += `
                                    <tr>
                                        <td class="text-center">
                                            <input class="form-check-input chk-mhs" type="checkbox" value="${mhs.id_registrasi_mahasiswa}">
                                        </td>
                                        <td>${mhs.nim}</td>
                                        <td>${mhs.nama_mahasiswa}</td>
                                        <td>${mhs.id_periode}</td>
                                    </tr>
                                `;
                            });
                        }
                        $('#bodyHasilPencarian').html(rows);
                        $('#hasilPencarianContainer').removeClass('d-none');
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan sistem saat mencari mahasiswa.', 'error');
                },
                complete: function() {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });

        // Check/Uncheck All
        $('#checkAllMhs').on('change', function() {
            $('.chk-mhs').prop('checked', $(this).prop('checked'));
        });

        // 2. Fungsi Simpan KRS Kolektif
        $('#btnSimpanPeserta').on('click', function() {
            let selectedIds = [];
            $('.chk-mhs:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                Swal.fire('Peringatan', 'Pilih minimal satu mahasiswa untuk ditambahkan ke kelas.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Tambahkan Peserta?',
                text: `Anda akan memasukkan ${selectedIds.length} mahasiswa ke dalam kelas ini.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tambahkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    let btn = $(this);
                    btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Menyimpan...');

                    $.ajax({
                        url: "{{ route('input-nilai.ajax.store-peserta') }}",
                        method: "POST",
                        data: {
                            id_kelas_kuliah: window.idKelas,
                            id_registrasi_mahasiswa: selectedIds
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('Berhasil', res.message, 'success');
                                $('#hasilPencarianContainer').addClass('d-none');
                                $('#modalKRS').modal('hide');
                                loadPesertaKelas(); // Reload tabel bawah
                            } else {
                                Swal.fire('Warning', res.message + '<br>' + (res.errors ? res
                                    .errors.join('<br>') : ''), 'warning');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Gagal menambahkan peserta.', 'error');
                        },
                        complete: function() {
                            btn.prop('disabled', false).html(
                                '<i class="bx bx-plus-circle"></i> Tambahkan ke Kelas');
                        }
                    });
                }
            });
        });

        // 3. Load Daftar Peserta Kelas (dari Feeder)
        function loadPesertaKelas() {
            $('#bodyPesertaKelas').html(
                '<tr><td colspan="11" class="text-center py-4"><i class="bx bx-loader bx-spin fs-2"></i><br>Memuat data peserta kelas...</td></tr>'
            );

            $.ajax({
                url: "/admin/input-nilai/ajax/peserta/" + window.idKelas,
                method: "GET",
                success: function(res) {
                    if (res.success) {
                        let rows = '';
                        if (res.data.length === 0) {
                            rows =
                                '<tr><td colspan="11" class="text-center text-muted py-4">Belum ada peserta di kelas ini.</td></tr>';
                        } else {
                            res.data.forEach(function(mhs, index) {
                                let nilai_angka = mhs.nilai_angka != null ? mhs.nilai_angka : '';
                                let nilai_huruf = mhs.nilai_huruf != null ? mhs.nilai_huruf : '-';
                                let nilai_indeks = mhs.nilai_indeks != null ? parseFloat(mhs.nilai_indeks).toFixed(2) : '0.00';

                                rows += `
                                    <tr class="mhs-row" data-id-reg="${mhs.id_registrasi_mahasiswa}" data-id-mhs="${mhs.id_mahasiswa}">
                                        <td class="text-center">${index + 1}</td>
                                        <td><strong>${mhs.nim}</strong><br><small>${mhs.nama_mahasiswa}</small></td>
                                        <td><input type="number" step="0.01" class="form-control form-control-sm inp-komponen inp-kuis" placeholder="0-100"></td>
                                        <td><input type="number" step="0.01" class="form-control form-control-sm inp-komponen inp-tugas1" placeholder="0-100"></td>
                                        <td><input type="number" step="0.01" class="form-control form-control-sm inp-komponen inp-tugas2" placeholder="0-100"></td>
                                        <td><input type="number" step="0.01" class="form-control form-control-sm inp-komponen inp-uts" placeholder="0-100"></td>
                                        <td><input type="number" step="0.01" class="form-control form-control-sm inp-komponen inp-uas" placeholder="0-100"></td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm fw-bold bg-light inp-angka" value="${nilai_angka}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-center fw-bold bg-light inp-huruf" value="${nilai_huruf}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-center fw-bold bg-light inp-indeks" value="${nilai_indeks}" readonly>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm btn-hapus-peserta" data-id="${mhs.id_registrasi_mahasiswa}" title="Hapus dari kelas">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `;
                            });
                        }
                        $('#bodyPesertaKelas').html(rows);
                        bindEventsToRows();
                    }
                },
                error: function(xhr) {
                    $('#bodyPesertaKelas').html(
                        '<tr><td colspan="11" class="text-center text-danger py-4">Gagal memuat daftar peserta.</td></tr>'
                    );
                }
            });
        }

        // 4. Kalkulator Komponen Nilai & Lookup Skala Nilai
        function calculateGrades(row) {
            let kuis = parseFloat(row.find('.inp-kuis').val()) || 0;
            let tugas1 = parseFloat(row.find('.inp-tugas1').val()) || 0;
            let tugas2 = parseFloat(row.find('.inp-tugas2').val()) || 0;
            let uts = parseFloat(row.find('.inp-uts').val()) || 0;
            let uas = parseFloat(row.find('.inp-uas').val()) || 0;

            // Bobot: Kuis 10%, Tugas1 10%, Tugas2 10%, UTS 30%, UAS 40%
            let nilai_akhir = (kuis * 0.10) + (tugas1 * 0.10) + (tugas2 * 0.10) + (uts * 0.30) + (uas * 0.40);

            // Limit to 2 decimal places
            nilai_akhir = parseFloat(nilai_akhir.toFixed(2));

            if (nilai_akhir > 0) {
                row.find('.inp-angka').val(nilai_akhir);
                updateHurufIndeks(row, nilai_akhir);
            }
        }

        // Cari Huruf & Indeks dari array Skala Nilai
        function updateHurufIndeks(row, nilai_angka) {
            let huruf = '-';
            let indeks = 0.00;

            // loop melalui window.skalaNilai
            for (let i = 0; i < window.skalaNilai.length; i++) {
                let skala = window.skalaNilai[i];
                if (nilai_angka >= parseFloat(skala.bobot_nilai_min) && nilai_angka <= parseFloat(skala.bobot_nilai_maks)) {
                    huruf = skala.nilai_huruf;
                    indeks = skala.nilai_indeks;
                    break; // ditemukan!
                }
            }

            row.find('.inp-huruf').val(huruf);
            row.find('.inp-indeks').val(parseFloat(indeks).toFixed(2));
        }

        function bindEventsToRows() {
            // Event ketika input komponen diubah
            $('.inp-komponen').on('input', function() {
                let row = $(this).closest('tr');
                calculateGrades(row);
            });

            // Event jika input angka diketik manual
            $('.inp-angka').on('input', function() {
                let row = $(this).closest('tr');
                let nilai_angka = parseFloat($(this).val()) || 0;
                updateHurufIndeks(row, nilai_angka);
            });

            // Hapus KRS Peserta
            $('.btn-hapus-peserta').on('click', function() {
                let id_reg = $(this).closest('tr').data('id-reg');

                Swal.fire({
                    title: 'Hapus Peserta?',
                    text: "Mahasiswa akan dikeluarkan dari kelas ini. Data nilai (jika ada) di Feeder juga akan hilang.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('input-nilai.ajax.destroy-peserta') }}",
                            method: "POST",
                            data: {
                                id_kelas_kuliah: window.idKelas,
                                id_registrasi_mahasiswa: id_reg
                            },
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire('Dihapus!', res.message, 'success');
                                    loadPesertaKelas();
                                } else {
                                    Swal.fire('Gagal', res.message, 'error');
                                }
                            }
                        });
                    }
                });
            });
        }

        // 5. Simpan Nilai Massal
        $('#btnSimpanNilai').on('click', function() {
            let dataNilai = [];

            $('.mhs-row').each(function() {
                let row = $(this);
                let id_reg = row.data('id-reg');
                let nilai_angka = row.find('.inp-angka').val();
                let nilai_huruf = row.find('.inp-huruf').val();
                let nilai_indeks = row.find('.inp-indeks').val();

                // Hanya simpan jika nilai angka sudah diisi
                if (nilai_angka !== "" && nilai_huruf !== "" && nilai_huruf !== "-") {
                    dataNilai.push({
                        id_registrasi_mahasiswa: id_reg,
                        nilai_angka: nilai_angka,
                        nilai_huruf: nilai_huruf,
                        nilai_indeks: nilai_indeks
                    });
                }
            });

            if (dataNilai.length === 0) {
                Swal.fire('Peringatan',
                    'Tidak ada data nilai yang siap disimpan. Pastikan nilai angka sudah terisi.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Simpan Nilai ke Feeder?',
                text: `Terdapat ${dataNilai.length} data nilai mahasiswa yang akan dipush ke Neo Feeder.`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let btn = $(this);
                    btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Menyimpan...');

                    $.ajax({
                        url: "{{ route('input-nilai.ajax.update-nilai') }}",
                        method: "POST",
                        data: {
                            id_kelas_kuliah: window.idKelas,
                            nilai_data: dataNilai
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('Berhasil', res.message, 'success');
                            } else {
                                Swal.fire('Peringatan', res.message + '<br>' + (res.errors ? res
                                    .errors.join('<br>') : ''), 'warning');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Terjadi kesalahan sistem saat menyimpan nilai.',
                                'error');
                        },
                        complete: function() {
                            btn.prop('disabled', false).html(
                                '<i class="bx bx-save"></i> Simpan Nilai Semua Mahasiswa');
                        }
                    });
                }
            });
        });

        // Initialize on Load
        $(document).ready(function() {
            loadPesertaKelas();
        });
    </script>
@endpush
