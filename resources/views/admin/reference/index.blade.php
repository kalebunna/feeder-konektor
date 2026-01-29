@extends('templates.layout')

@section('title', 'Sinkronisasi Referansi')
@section('page-title', 'Sinkronisasi Referansi')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="page-title">Tabel Referensi</h3>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Tabel</th>
                                    <th>Aksi Neo Feeder</th>
                                    <th>Total Lokal</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($references as $ref)
                                    <tr>
                                        <td>{{ $ref['name'] }}</td>
                                        <td><span class="badge badge-info">{{ $ref['act'] }}</span></td>
                                        <td id="count-{{ $ref['table'] }}">{{ $ref['model']::count() }}</td>
                                        <td class="text-end">
                                            <div class="actions">
                                                <button class="btn btn-primary btn-sm sync-btn"
                                                    data-table="{{ $ref['table'] }}">
                                                    <i class="fas fa-sync me-1"></i> Sync
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                $('.sync-btn').on('click', function() {
                    var btn = $(this);
                    var table = btn.data('table');
                    var originalText = btn.html();

                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Syncing...');

                    $.ajax({
                        url: "{{ url('admin/reference/sync') }}/" + table,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                // Refresh count - actually better to reload from server or just update local
                                $('#count-' + table).text(response.count);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat sinkronisasi.'
                            });
                        },
                        complete: function() {
                            btn.prop('disabled', false).html(originalText);
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
