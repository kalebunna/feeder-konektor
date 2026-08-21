@extends('templates.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Utama')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card bg-primary-subtle border-0 mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-primary fw-bold mb-2">
                                Selamat Datang, {{ Auth::user()->name ?? 'Administrator' }}! 👋
                            </h3>
                            <p class="text-muted font-size-15 mb-3">
                                Anda telah masuk ke sistem <strong>Feeder Konektor STAINAS</strong>. Sistem ini berfungsi sebagai jembatan integrasi, sinkronisasi master data, dan pengelolaan nilai perkuliahan antara SIAKAD dan Neo Feeder PDDIKTI.
                            </p>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('prodi.index') }}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-briefcase me-1"></i> Program Studi
                                </a>
                                <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bx bx-user me-1"></i> Daftar Mahasiswa
                                </a>
                                <a href="{{ route('input-nilai.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bx bx-edit me-1"></i> Input Nilai & KRS
                                </a>
                                <a href="{{ route('api-tokens.index') }}" class="btn btn-outline-success btn-sm">
                                    <i class="bx bx-key me-1"></i> API Tokens (Sanctum)
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 text-center mt-3 mt-md-0">
                            <div class="avatar-lg mx-auto bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px;">
                                <i class="bx bx-shield-quarter text-primary" style="font-size: 54px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
