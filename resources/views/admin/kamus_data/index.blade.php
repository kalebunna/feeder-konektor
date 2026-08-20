@extends('templates.layout')

@section('title', 'Kamus Data Feeder')
@section('page-title', 'Kamus Data / Dictionary Feeder')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Cari Kamus Data (GetDictionary)</h4>
                    <p class="card-title-desc">Masukkan nama aksi (contoh: <code>InsertKelasKuliah</code>) untuk melihat daftar kolom dan tipe datanya.</p>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Contoh Pencarian Cepat:</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="fillAndSubmit('GetListMahasiswa')">GetListMahasiswa</button>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="fillAndSubmit('InsertKelasKuliah')">InsertKelasKuliah</button>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="fillAndSubmit('InsertPesertaKelasKuliah')">InsertPesertaKelasKuliah</button>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="fillAndSubmit('UpdateNilaiPerkuliahanKelas')">UpdateNilaiPerkuliahanKelas</button>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="fillAndSubmit('InsertNilaiPerkuliahanKelas')">InsertNilaiPerkuliahanKelas</button>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="fillAndSubmit('GetDetailNilaiPerkuliahanKelas')">GetDetailNilaiPerkuliahanKelas</button>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="fillAndSubmit('GetRiwayatPendidikanMahasiswa')">GetRiwayatPendidikanMahasiswa</button>
                        </div>
                    </div>

                    <form id="kamusForm">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-6 mb-3">
                                <label for="fungsiInput" class="form-label">Nama Aksi (act / fungsi)</label>
                                <input type="text" class="form-control" id="fungsiInput" name="fungsi" placeholder="Contoh: InsertKurikulum" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <button type="submit" class="btn btn-primary w-md" id="btnSubmit">
                                    <span class="spinner-border spinner-border-sm d-none" id="spinner"></span>
                                    <i class="bx bx-search-alt align-middle me-1"></i> Cari Kamus Data
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id="errorAlert" class="alert alert-danger d-none mt-3"></div>

                    <div class="mt-4 d-none" id="resultContainer">
                        <h5 class="font-size-14 mb-3">Hasil Kamus Data: <span id="lblFungsi" class="text-primary fw-bold"></span></h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th style="width: 25%">Nama Kolom</th>
                                        <th style="width: 15%">Tipe Data</th>
                                        <th style="width: 10%">Wajib Diisi (Mandatory)</th>
                                        <th style="width: 45%">Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <!-- Data will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function fillAndSubmit(fungsiName) {
            $('#fungsiInput').val(fungsiName);
            $('#kamusForm').submit();
        }

        $('#kamusForm').on('submit', function(e) {
            e.preventDefault();

            const fungsiName = $('#fungsiInput').val().trim();
            if (!fungsiName) return;

            const btn = $('#btnSubmit');
            const spinner = $('#spinner');
            const resultContainer = $('#resultContainer');
            const errorAlert = $('#errorAlert');
            const tbody = $('#tableBody');

            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            resultContainer.addClass('d-none');
            errorAlert.addClass('d-none');
            tbody.empty();

            $.ajax({
                url: "{{ route('kamus-data.fetch') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    fungsi: fungsiName
                },
                success: function(response) {
                    if (response.error_code && response.error_code !== 0) {
                        errorAlert.removeClass('d-none').html('<strong>Error:</strong> ' + response.error_desc);
                    } else if (response.data && (response.data.request || response.data.response)) {
                        $('#lblFungsi').text(fungsiName);
                        
                        let rows = '';
                        
                        // Parse Request Dictionary
                        if (response.data.request) {
                            rows += '<tr><td colspan="5" class="bg-light fw-bold text-primary">Parameter Request (Yang harus dikirim)</td></tr>';
                            let i = 1;
                            for (let key in response.data.request) {
                                let item = response.data.request[key];
                                if(typeof item === 'object') {
                                    let isMandatory = (item.nullable === 'not null' || item.nullable === '1');
                                    let mandatoryBadge = isMandatory 
                                        ? '<span class="badge bg-danger">Wajib</span>' 
                                        : '<span class="badge bg-secondary">Opsional</span>';
                                        
                                    rows += `<tr>
                                                <td class="text-center">${i++}</td>
                                                <td class="fw-medium"><code>${key}</code></td>
                                                <td>${item.type || '-'} ${item.primary === 'primary' ? '<span class="badge bg-warning text-dark ms-1">PK</span>' : ''}</td>
                                                <td class="text-center">${mandatoryBadge}</td>
                                                <td>${item.keterangan || '-'}</td>
                                            </tr>`;
                                } else {
                                    // if it's just a string like "token": ""
                                    rows += `<tr>
                                                <td class="text-center">${i++}</td>
                                                <td class="fw-medium"><code>${key}</code></td>
                                                <td>string</td>
                                                <td class="text-center"><span class="badge bg-danger">Wajib</span></td>
                                                <td>-</td>
                                            </tr>`;
                                }
                            }
                        }

                        // Parse Response Dictionary
                        if (response.data.response) {
                            rows += '<tr><td colspan="5" class="bg-light fw-bold text-success">Parameter Response (Yang akan dikembalikan)</td></tr>';
                            let i = 1;
                            for (let key in response.data.response) {
                                let item = response.data.response[key];
                                if(typeof item === 'object') {
                                    rows += `<tr>
                                                <td class="text-center">${i++}</td>
                                                <td class="fw-medium"><code>${key}</code></td>
                                                <td>${item.type || '-'} ${item.primary === 'primary' ? '<span class="badge bg-warning text-dark ms-1">PK</span>' : ''}</td>
                                                <td class="text-center">-</td>
                                                <td>${item.keterangan || '-'}</td>
                                            </tr>`;
                                }
                            }
                        }
                        
                        tbody.html(rows);
                        resultContainer.removeClass('d-none');
                    } else {
                        errorAlert.removeClass('d-none').html('Tidak ada data kamus untuk aksi "<strong>' + fungsiName + '</strong>". Pastikan nama aksi benar.');
                    }
                },
                error: function(xhr) {
                    let msg = 'Terjadi kesalahan saat mengambil data.';
                    if (xhr.responseJSON && xhr.responseJSON.error_desc) {
                        msg = xhr.responseJSON.error_desc;
                    }
                    errorAlert.removeClass('d-none').html('<strong>Gagal:</strong> ' + msg);
                },
                complete: function() {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });
    </script>
@endpush
