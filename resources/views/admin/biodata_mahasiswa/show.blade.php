@extends('templates.layout')

@section('title', 'Detail Biodata Mahasiswa')
@section('page-title', 'Detail Biodata Mahasiswa')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Biodata: {{ $mahasiswa->nama_mahasiswa }}</h4>
                    <a href="{{ route('biodata-mahasiswa.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <!-- Tab Profil -->
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            <div class="profile-avatar mb-3">
                                <img src="{{ asset('templates/assets/images/users/avatar-1.jpg') }}" alt=""
                                    class="img-thumbnail rounded-circle" style="width: 150px; height: 150px;">
                            </div>
                            <h5 class="mb-1">{{ $mahasiswa->nama_mahasiswa }}</h5>
                            <p class="text-muted">{{ $mahasiswa->id_mahasiswa }}</p>
                            <span
                                class="badge {{ $mahasiswa->status_sync == 'sudah sync' ? 'bg-success' : 'bg-warning' }} font-size-12">
                                {{ ucfirst($mahasiswa->status_sync) }}
                            </span>
                        </div>
                        <div class="col-md-9">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#personal" role="tab">
                                        <span class="d-block d-sm-none"><i class="fas fa-user"></i></span>
                                        <span class="d-none d-sm-block">Data Pribadi</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#address" role="tab">
                                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                        <span class="d-none d-sm-block">Alamat & Kontak</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#family" role="tab">
                                        <span class="d-block d-sm-none"><i class="fas fa-users"></i></span>
                                        <span class="d-none d-sm-block">Keluarga</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#other" role="tab">
                                        <span class="d-block d-sm-none"><i class="fas fa-info-circle"></i></span>
                                        <span class="d-none d-sm-block">Lainnya</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#academic" role="tab">
                                        <span class="d-block d-sm-none"><i class="fas fa-graduation-cap"></i></span>
                                        <span class="d-none d-sm-block">Data Akademik</span>
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content p-3 text-muted">
                                <div class="tab-pane active" id="personal" role="tabpanel">
                                    <table class="table table-nowrap mb-0">
                                        <tbody>
                                            <tr>
                                                <th scope="row" style="width: 200px;">Nama Lengkap</th>
                                                <td>{{ $mahasiswa->nama_mahasiswa }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Jenis Kelamin</th>
                                                <td>{{ $mahasiswa->jenis_kelamin == 'P' ? 'Perempuan' : ($mahasiswa->jenis_kelamin == 'L' ? 'Laki-laki' : $mahasiswa->jenis_kelamin) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Tempat, Tanggal Lahir</th>
                                                <td>{{ $mahasiswa->tempat_lahir }},
                                                    {{ \Carbon\Carbon::parse($mahasiswa->tanggal_lahir)->translatedFormat('d F Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Agama</th>
                                                <td>{{ $mahasiswa->nama_agama }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">NIK</th>
                                                <td>{{ $mahasiswa->nik }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">NISN</th>
                                                <td>{{ $mahasiswa->nisn }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">NPWP</th>
                                                <td>{{ $mahasiswa->npwp ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Kewarganegaraan</th>
                                                <td>{{ $mahasiswa->kewarganegaraan }} ({{ $mahasiswa->id_negara }})</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="address" role="tabpanel">
                                    <table class="table table-nowrap mb-0">
                                        <tbody>
                                            <tr>
                                                <th scope="row" style="width: 200px;">Jalan</th>
                                                <td>{{ $mahasiswa->jalan ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Dusun</th>
                                                <td>{{ $mahasiswa->dusun }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">RT / RW</th>
                                                <td>RT {{ $mahasiswa->rt }} / RW {{ $mahasiswa->rw }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Kelurahan</th>
                                                <td>{{ $mahasiswa->kelurahan }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Kecamatan (Wilayah)</th>
                                                <td>{{ $mahasiswa->nama_wilayah }} ({{ $mahasiswa->id_wilayah }})</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Kode Pos</th>
                                                <td>{{ $mahasiswa->kode_pos }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Jenis Tinggal</th>
                                                <td>{{ $mahasiswa->nama_jenis_tinggal }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Alat Transportasi</th>
                                                <td>{{ $mahasiswa->nama_alat_transportasi }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Email</th>
                                                <td>{{ $mahasiswa->email }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Handphone / Telepon</th>
                                                <td>{{ $mahasiswa->handphone ?? '-' }} / {{ $mahasiswa->telepon ?? '-' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="family" role="tabpanel">
                                    <div class="mb-4">
                                        <h6 class="text-primary"><i class="fas fa-male me-2"></i> Data Ayah</h6>
                                        <table class="table table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row" style="width: 200px;">Nama Ayah</th>
                                                    <td>{{ $mahasiswa->nama_ayah }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">NIK Ayah</th>
                                                    <td>{{ $mahasiswa->nik_ayah }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Tanggal Lahir Ayah</th>
                                                    <td>{{ $mahasiswa->tanggal_lahir_ayah ? \Carbon\Carbon::parse($mahasiswa->tanggal_lahir_ayah)->translatedFormat('d F Y') : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Pendidikan Ayah</th>
                                                    <td>{{ $mahasiswa->nama_pendidikan_ayah ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Pekerjaan Ayah</th>
                                                    <td>{{ $mahasiswa->nama_pekerjaan_ayah ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Penghasilan Ayah</th>
                                                    <td>{{ $mahasiswa->nama_penghasilan_ayah ?? '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="text-primary"><i class="fas fa-female me-2"></i> Data Ibu</h6>
                                        <table class="table table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row" style="width: 200px;">Nama Ibu Kandung</th>
                                                    <td>{{ $mahasiswa->nama_ibu_kandung }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">NIK Ibu</th>
                                                    <td>{{ $mahasiswa->nik_ibu }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Tanggal Lahir Ibu</th>
                                                    <td>{{ $mahasiswa->tanggal_lahir_ibu ? \Carbon\Carbon::parse($mahasiswa->tanggal_lahir_ibu)->translatedFormat('d F Y') : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Pendidikan Ibu</th>
                                                    <td>{{ $mahasiswa->nama_pendidikan_ibu ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Pekerjaan Ibu</th>
                                                    <td>{{ $mahasiswa->nama_pekerjaan_ibu ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Penghasilan Ibu</th>
                                                    <td>{{ $mahasiswa->nama_penghasilan_ibu ?? '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ($mahasiswa->nama_wali)
                                        <div>
                                            <h6 class="text-primary"><i class="fas fa-user-shield me-2"></i> Data Wali
                                            </h6>
                                            <table class="table table-nowrap mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th scope="row" style="width: 200px;">Nama Wali</th>
                                                        <td>{{ $mahasiswa->nama_wali }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Pendidikan Wali</th>
                                                        <td>{{ $mahasiswa->nama_pendidikan_wali ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Pekerjaan Wali</th>
                                                        <td>{{ $mahasiswa->nama_pekerjaan_wali ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Penghasilan Wali</th>
                                                        <td>{{ $mahasiswa->nama_penghasilan_wali ?? '-' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                                <div class="tab-pane" id="other" role="tabpanel">
                                    <table class="table table-nowrap mb-0">
                                        <tbody>
                                            <tr>
                                                <th scope="row" style="width: 200px;">Penerima KPS</th>
                                                <td>{{ $mahasiswa->penerima_kps == '1' ? 'Ya' : 'Tidak' }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Nomor KPS</th>
                                                <td>{{ $mahasiswa->nomor_kps ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Kebutuhan Khusus Mahasiswa</th>
                                                <td>{{ $mahasiswa->nama_kebutuhan_khusus_mahasiswa }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Kebutuhan Khusus Ayah</th>
                                                <td>{{ $mahasiswa->nama_kebutuhan_khusus_ayah }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Kebutuhan Khusus Ibu</th>
                                                <td>{{ $mahasiswa->nama_kebutuhan_khusus_ibu }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="academic" role="tabpanel">
                                    @if ($mahasiswa->mahasiswa)
                                        @if (!$mahasiswa->mahasiswa->id_registrasi_mahasiswa || !$mahasiswa->mahasiswa->nim)
                                            <div class="alert alert-warning border-0 d-flex align-items-center mb-4"
                                                role="alert">
                                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                                <div>
                                                    <strong>Data Belum Lengkap!</strong> Mahasiswa ini ditemukan di daftar
                                                    registrasi namun belum memiliki NIM atau ID Registrasi yang valid di
                                                    NeoFeeder.
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info border-0 d-flex align-items-center mb-4"
                                                role="alert">
                                                <i data-feather="info" class="text-info me-2"></i>
                                                <div>
                                                    Ditemukan data registrasi akademik untuk mahasiswa ini.
                                                </div>
                                            </div>
                                        @endif

                                        <table class="table table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row" style="width: 200px;">NIM</th>
                                                    <td class="fw-bold text-primary">
                                                        {{ $mahasiswa->mahasiswa->nim ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Program Studi</th>
                                                    <td>{{ $mahasiswa->mahasiswa->nama_program_studi ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Periode Masuk</th>
                                                    <td>{{ $mahasiswa->mahasiswa->nama_periode_masuk ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Status Mahasiswa</th>
                                                    <td>
                                                        @if ($mahasiswa->mahasiswa->nama_status_mahasiswa)
                                                            <span
                                                                class="badge {{ $mahasiswa->mahasiswa->nama_status_mahasiswa == 'AKTIF' ? 'bg-success' : 'bg-secondary' }}">
                                                                {{ $mahasiswa->mahasiswa->nama_status_mahasiswa }}
                                                            </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">IPK</th>
                                                    <td>{{ $mahasiswa->mahasiswa->ipk ?? '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Total SKS</th>
                                                    <td>{{ $mahasiswa->mahasiswa->total_sks ?? 0 }} SKS</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">ID Registrasi</th>
                                                    <td><code
                                                            class="text-muted">{{ $mahasiswa->mahasiswa->id_registrasi_mahasiswa ?? '-' }}</code>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Status Keluar</th>
                                                    <td>{{ $mahasiswa->mahasiswa->id_periode_keluar ? 'Keluar pada periode ' . $mahasiswa->mahasiswa->id_periode_keluar : 'Belum Lulus/Keluar' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="alert alert-warning border-0 d-flex align-items-center py-4"
                                            role="alert">
                                            <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
                                            <div>
                                                <h5 class="alert-heading text-warning mb-1">Data Belum Lengkap</h5>
                                                <p class="mb-0">Daftar registrasi akademik mahasiswa ini belum
                                                    di-sinkronkan atau tidak ditemukan di NeoFeeder.</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
