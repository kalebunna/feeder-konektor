@extends('templates.layout')

@section('title', 'Profil Perguruan Tinggi')
@section('page-title', 'Profil PT')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary bg-soft">
                    <h4 class="card-title text-primary">Informasi Umum</h4>
                </div>
                <div class="card-body">
                    @if ($profil)
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-nowrap mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="width: 30%;">Nama Perguruan Tinggi</th>
                                            <td>: {{ $profil->nama_perguruan_tinggi }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Kode PT</th>
                                            <td>: {{ $profil->kode_perguruan_tinggi }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Email</th>
                                            <td>: {{ $profil->email }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Telepon</th>
                                            <td>: {{ $profil->telepon }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Website</th>
                                            <td>: <a href="http://{{ $profil->website }}"
                                                    target="_blank">{{ $profil->website }}</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-nowrap mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="width: 30%;">Wilayah</th>
                                            <td>: {{ $profil->nama_wilayah }} ({{ $profil->id_wilayah }})</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Alamat</th>
                                            <td>: {{ $profil->jalan }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Kode Pos</th>
                                            <td>: {{ $profil->kode_pos }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Koordinat</th>
                                            <td>: {{ $profil->lintang_bujur }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <div class="card border shadow-none">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Informasi Tambahan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="p-3 border rounded text-center mb-3">
                                                    <h6 class="text-muted mb-1">Luas Tanah Milik</h6>
                                                    <h5 class="mb-0">{{ $profil->luas_tanah_milik }} m<sup>2</sup></h5>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="p-3 border rounded text-center mb-3">
                                                    <h6 class="text-muted mb-1">Luas Tanah Bukan Milik</h6>
                                                    <h5 class="mb-0">{{ $profil->luas_tanah_bukan_milik }} m<sup>2</sup>
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="p-3 border rounded text-center mb-3">
                                                    <h6 class="text-muted mb-1">MBS</h6>
                                                    <h5 class="mb-0">{{ $profil->mbs == '1' ? 'Ya' : 'Tidak' }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center p-4">
                            <i class="mdi mdi-database-off display-4 text-muted"></i>
                            <h5 class="mt-3">Data Profil Belum Tersedia</h5>
                            <p class="text-muted">Silakan lakukan sinkronisasi data dari Feeder PDDIKTI.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
