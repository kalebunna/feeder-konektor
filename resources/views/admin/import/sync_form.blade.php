@extends('templates.layout')

@section('title', 'Sinkronisasi Biodata Mahasiswa')
@section('page-title', 'Sinkronisasi Biodata Mahasiswa')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form id="syncForm" action="{{ route('import-mahasiswa.sync-process', $mhs->id) }}" method="POST">
                @csrf

                <!-- BIODATA SECTION -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 text-white"><i class="fas fa-user me-2"></i>Bagian 1: Biodata Mahasiswa
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nama Mahasiswa</label>
                                <input type="text" name="nama_mahasiswa" class="form-control" value="{{ $mhs->nama }}"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="L" {{ $mhs->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>
                                        Laki-laki</option>
                                    <option value="P" {{ $mhs->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                @php
                                    $tempatLahir = $mhs->tetala ? explode(',', $mhs->tetala)[0] : '';
                                @endphp
                                <input type="text" name="tempat_lahir" class="form-control"
                                    value="{{ trim($tempatLahir) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="{{ $tanggalLahir }}"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Agama</label>
                                <select name="id_agama" class="form-control select2" required>
                                    <option value="">-- Pilih Agama --</option>
                                    @foreach ($agamas as $agama)
                                        <option value="{{ $agama->id_agama }}"
                                            {{ $selectedAgama == $agama->id_agama ? 'selected' : '' }}>
                                            {{ $agama->nama_agama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">NIK</label>
                                <input type="text" name="nik" class="form-control" value="{{ $mhs->nik }}"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nomor Handphone</label>
                                <input type="text" name="handphone" class="form-control" value="{{ $mhs->nomor_hp }}"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $defaultEmail }}"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kewarganegaraan</label>
                                <select name="kewarganegaraan" class="form-control select2" required>
                                    @foreach ($negaras as $negara)
                                        <option value="{{ $negara->id_negara }}"
                                            {{ $selectedNegara == $negara->id_negara ? 'selected' : '' }}>
                                            {{ $negara->nama_negara }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Wilayah (Kecamatan)</label>
                                <select name="id_wilayah" class="form-control select2" required>
                                    <option value="">-- Pilih Wilayah --</option>
                                    @if ($matchedWilayah)
                                        <option value="{{ $matchedWilayah->id_wilayah }}" selected>
                                            {{ $matchedWilayah->nama_wilayah }}</option>
                                    @endif
                                </select>
                                <small class="text-muted">Gunakan pencarian untuk wilayah lain (Coming Soon with
                                    AJAX)</small>
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold mb-3"><i class="fas fa-home me-2"></i>Alamat</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dusun</label>
                                <input type="text" name="dusun" class="form-control" value="{{ $mhs->dusun }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">RT</label>
                                @php
                                    $rt = $mhs->rt_rw ? explode('/', $mhs->rt_rw)[0] : 0;
                                @endphp
                                <input type="number" name="rt" class="form-control" value="{{ trim($rt) }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">RW</label>
                                @php
                                    $rw = $mhs->rt_rw ? explode('/', $mhs->rt_rw)[1] ?? 0 : 0;
                                @endphp
                                <input type="number" name="rw" class="form-control" value="{{ trim($rw) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kelurahan</label>
                                <input type="text" name="kelurahan" class="form-control"
                                    value="{{ $mhs->desa_kelurahan }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PARENTS SECTION -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0 text-white"><i class="fas fa-users me-2"></i>Bagian 2: Data Orang Tua
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Ayah -->
                            <div class="col-md-6 border-end">
                                <h6 class="fw-bold text-primary mb-3">Data Ayah</h6>
                                <div class="mb-3">
                                    <label class="form-label">Nama Ayah</label>
                                    <input type="text" name="nama_ayah" class="form-control"
                                        value="{{ $mhs->nama_ayah }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIK Ayah</label>
                                        <input type="text" name="nik_ayah" class="form-control"
                                            value="{{ $mhs->nik_ayah }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Lahir Ayah</label>
                                        <input type="date" name="tanggal_lahir_ayah" class="form-control"
                                            value="{{ $mhs->tanggal_lahir_ayah }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Pendidikan Ayah</label>
                                    <select name="id_pendidikan_ayah" class="form-control select2">
                                        <option value="">-- Pilih Pendidikan --</option>
                                        @foreach ($pendidikans as $p)
                                            <option value="{{ $p->id_jenjang_didik }}"
                                                {{ $selectedPendidikanAyah == $p->id_jenjang_didik ? 'selected' : '' }}>
                                                {{ $p->nama_jenjang_didik }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Pekerjaan Ayah</label>
                                        <select name="id_pekerjaan_ayah" class="form-control select2">
                                            <option value="">-- Pilih Pekerjaan --</option>
                                            @foreach ($pekerjaans as $pk)
                                                <option value="{{ $pk->id_pekerjaan }}"
                                                    {{ $selectedPekerjaanAyah == $pk->id_pekerjaan ? 'selected' : '' }}>
                                                    {{ $pk->nama_pekerjaan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Penghasilan Ayah</label>
                                        <select name="id_penghasilan_ayah" class="form-control select2">
                                            <option value="">-- Pilih Penghasilan --</option>
                                            @foreach ($penghasilans as $pen)
                                                <option value="{{ $pen->id_penghasilan }}">{{ $pen->nama_penghasilan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Ibu -->
                            <div class="col-md-6">
                                <h6 class="fw-bold text-danger mb-3">Data Ibu Nama Kandung</h6>
                                <div class="mb-3">
                                    <label class="form-label">Nama Ibu</label>
                                    <input type="text" name="nama_ibu_kandung" class="form-control"
                                        value="{{ $mhs->nama_ibu }}" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIK Ibu</label>
                                        <input type="text" name="nik_ibu" class="form-control"
                                            value="{{ $mhs->nik_ibu }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Lahir Ibu</label>
                                        <input type="date" name="tanggal_lahir_ibu" class="form-control"
                                            value="{{ $mhs->tanggal_lahir_ibu }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Pendidikan Ibu</label>
                                    <select name="id_pendidikan_ibu" class="form-control select2">
                                        <option value="">-- Pilih Pendidikan --</option>
                                        @foreach ($pendidikans as $p)
                                            <option value="{{ $p->id_jenjang_didik }}"
                                                {{ $selectedPendidikanIbu == $p->id_jenjang_didik ? 'selected' : '' }}>
                                                {{ $p->nama_jenjang_didik }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Pekerjaan Ibu</label>
                                        <select name="id_pekerjaan_ibu" class="form-control select2">
                                            <option value="">-- Pilih Pekerjaan --</option>
                                            @foreach ($pekerjaans as $pk)
                                                <option value="{{ $pk->id_pekerjaan }}"
                                                    {{ $selectedPekerjaanIbu == $pk->id_pekerjaan ? 'selected' : '' }}>
                                                    {{ $pk->nama_pekerjaan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Penghasilan Ibu</label>
                                        <select name="id_penghasilan_ibu" class="form-control select2">
                                            <option value="">-- Pilih Penghasilan --</option>
                                            @foreach ($penghasilans as $pen)
                                                <option value="{{ $pen->id_penghasilan }}">{{ $pen->nama_penghasilan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGISTRATION SECTION -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0 text-white"><i class="fas fa-id-card me-2"></i>Bagian 3: Data
                            Registrasi Mahasiswa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">NIM</label>
                                <input type="text" name="nim" class="form-control" value="{{ $mhs->nim }}"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Program Studi</label>
                                <select name="id_prodi" class="form-control select2" required>
                                    <option value="">-- Pilih Prodi --</option>
                                    @foreach ($prodis as $prodi)
                                        <option value="{{ $prodi->id_prodi }}"
                                            {{ strpos($mhs->program_studi, $prodi->nama_program_studi) !== false ? 'selected' : '' }}>
                                            {{ $prodi->nama_program_studi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jenis Pendaftaran</label>
                                <select name="id_jenis_daftar" class="form-control select2" required>
                                    @foreach ($jenisPendaftarans as $jp)
                                        <option value="{{ $jp->id_jenis_daftar }}"
                                            {{ $jp->nama_jenis_daftar == 'Peserta didik baru' ? 'selected' : '' }}>
                                            {{ $jp->nama_jenis_daftar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jalur Pendaftaran</label>
                                <select name="id_jalur_daftar" class="form-control select2" required>
                                    @foreach ($jalurMasuks as $jm)
                                        <option value="{{ $jm->id_jalur_masuk }}">{{ $jm->nama_jalur_masuk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Periode Masuk</label>
                                <select name="id_periode_masuk" class="form-control select2" required>
                                    <option value="">-- Pilih Periode --</option>
                                    @foreach ($semesters as $index => $s)
                                        <option value="{{ $s->id_semester }}"
                                            data-start="{{ $s->tanggal_mulai ? $s->tanggal_mulai->format('Y-m-d') : '' }}"
                                            {{ $index == 0 ? 'selected' : '' }}>
                                            {{ $s->nama_semester }}</option>
                                    @endforeach
                                    @if ($semesters->isEmpty())
                                        <option value="">-- Tidak ada semester aktif --</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jenis Pembiayaan</label>
                                <select name="id_pembiayaan" class="form-control select2" required>
                                    <option value="">-- Pilih Pembiayaan --</option>
                                    @foreach ($pembiayaans as $pb)
                                        <option value="{{ $pb->id_pembiayaan }}"
                                            {{ $pb->nama_pembiayaan == 'Mandiri' ? 'selected' : '' }}>
                                            {{ $pb->nama_pembiayaan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Daftar</label>
                                <input type="date" name="tanggal_daftar" id="tanggal_daftar" class="form-control"
                                    value="{{ $semesters->first()?->tanggal_mulai ? $semesters->first()->tanggal_mulai->format('Y-m-d') : '' }}"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Biaya Masuk</label>
                                <input type="number" name="biaya_masuk" class="form-control" value="0">
                            </div>
                            <input type="hidden" name="id_perguruan_tinggi"
                                value="{{ $profilPT->id_perguruan_tinggi ?? '' }}">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-5">
                    <a href="{{ route('import-mahasiswa.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-5"><i class="fas fa-sync me-2"></i>Sinkronkan ke
                        Feeder</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });

            $('select[name="id_periode_masuk"]').on('change', function() {
                var start = $(this).find(':selected').data('start');
                if (start) {
                    $('#tanggal_daftar').val(start);
                }
            });

            $('#syncForm').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Sedang Sinkronisasi...',
                    text: 'Mohon tunggu sebentar, data sedang dikirim ke NeoFeeder.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                            }).then(() => {
                                window.location.href =
                                    "{{ route('import-mahasiswa.index') }}";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Sinkronisasi',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan sistem. Silakan coba lagi.',
                        });
                    }
                });
            });
        });
    </script>
@endpush
