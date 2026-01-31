@extends('templates.layout')

@section('title', 'Daftar Kelas Kuliah')
@section('page-title', 'Daftar Kelas Kuliah')

@push('css')
    <!-- DataTables -->
    <link href="{{ asset('templates/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('templates/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
        rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title">Data Kelas Kuliah dari Feeder</h4>
                        <p class="text-muted mb-0">Menampilkan data langsung dari Neo Feeder tanpa melalui database lokal.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Pilih Semester</label>
                            <select class="form-control" id="filter-semester">
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id_semester }}"
                                        {{ $selectedSemesterId == $semester->id_semester ? 'selected' : '' }}>
                                        {{ $semester->nama_semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive nowrap w-100" id="kelas-kuliah-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Dosen</th>
                                    <th>Program Studi</th>
                                    <th>Nama Kelas</th>
                                    <th>Kode Matkul</th>
                                    <th>Nama Matkul</th>
                                    <th>Semester</th>
                                    <th>Jml Mhs</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
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

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#kelas-kuliah-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kelas-kuliah.index') }}",
                    data: function(d) {
                        d.id_semester = $('#filter-semester').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_dosen',
                        name: 'nama_dosen'
                    },
                    {
                        data: 'nama_program_studi',
                        name: 'nama_program_studi'
                    },
                    {
                        data: 'nama_kelas_kuliah',
                        name: 'nama_kelas_kuliah'
                    },
                    {
                        data: 'kode_mata_kuliah',
                        name: 'kode_mata_kuliah'
                    },
                    {
                        data: 'nama_mata_kuliah',
                        name: 'nama_mata_kuliah'
                    },
                    {
                        data: 'nama_semester',
                        name: 'nama_semester'
                    },
                    {
                        data: 'jumlah_mahasiswa',
                        name: 'jumlah_mahasiswa'
                    },
                ]
            });

            $('#filter-semester').change(function() {
                table.draw();
            });
        });
    </script>
@endpush
