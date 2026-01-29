@extends('templates.layout')

@section('title', 'Daftar Program Studi')
@section('page-title', 'Program Studi')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Program Studi</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Prodi</th>
                                    <th>Nama Program Studi</th>
                                    <th>Jenjang</th>
                                    <th>Status</th>
                                    <th>ID Prodi (Feeder)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($prodis as $prodi)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $prodi->kode_program_studi }}</td>
                                        <td>{{ $prodi->nama_program_studi }}</td>
                                        <td>{{ $prodi->nama_jenjang_pendidikan }}</td>
                                        <td>
                                            <span class="badge {{ $prodi->status == 'A' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $prodi->status == 'A' ? 'Aktif' : 'Non-Aktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <code>{{ $prodi->id_prodi }}</code>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">Detail</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Data masih kosong.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
