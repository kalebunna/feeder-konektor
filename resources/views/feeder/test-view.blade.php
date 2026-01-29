@extends('templates.layout')

@section('title', 'Test Feeder')
@section('page-title', 'JSON Explorer Feeder')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Kirim Request JSON ke Feeder</h4>
                    <p class="card-title-desc">Masukkan JSON payload sesuai dokumentasi Feeder. Token akan ditambahkan secara
                        otomatis oleh sistem.</p>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Coba Cepat (Klik untuk isi):</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-info"
                                onclick="fillJson('GetProfilPT')">Profil PT</button>
                            <button type="button" class="btn btn-sm btn-outline-info"
                                onclick="fillJson('GetDictionary')">Kamus Feeder</button>
                            <button type="button" class="btn btn-sm btn-outline-info"
                                onclick="fillJson('GetListMahasiswa')">Daftar Mahasiswa (Limit 5)</button>
                        </div>
                    </div>

                    <form id="feederForm">
                        @csrf
                        <div class="mb-3">
                            <label for="jsonPayload" class="form-label">JSON Payload</label>
                            <textarea class="form-control" id="jsonPayload" rows="8" placeholder='{"act": "GetProfilPT"}'></textarea>
                            <small class="text-muted">Pastikan diawali dengan <code>{</code> dan diakhiri dengan
                                <code>}</code> serta menggunakan tanda kutip dua <code>"</code>.</small>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary w-md" id="btnSubmit">
                                <span class="spinner-border spinner-border-sm d-none" id="spinner"></span>
                                Kirim Request
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 d-none" id="resultContainer">
                        <label class="form-label">Response Server:</label>
                        <div class="bg-light p-3 rounded">
                            <pre id="jsonResult" class="mb-0" style="max-height: 500px; overflow-y: auto;"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function fillJson(action) {
            let payload = '';
            switch (action) {
                case 'GetProfilPT':
                    payload = '{\n    "act": "GetProfilPT"\n}';
                    break;
                case 'GetDictionary':
                    payload = '{\n    "act": "GetDictionary",\n    "fungsi": "GetListMahasiswa"\n}';
                    break;
                case 'GetListMahasiswa':
                    payload = '{\n    "act": "GetListMahasiswa",\n    "limit": 5\n}';
                    break;
            }
            $('#jsonPayload').val(payload);
        }

        $('#feederForm').on('submit', function(e) {
            e.preventDefault();

            const payload = $('#jsonPayload').val();
            const btn = $('#btnSubmit');
            const spinner = $('#spinner');
            const resultContainer = $('#resultContainer');
            const jsonResult = $('#jsonResult');

            try {
                JSON.parse(payload);
            } catch (e) {
                alert('Format JSON tidak valid! Pastikan Anda menyertakan kurung kurawal { } di awal dan akhir teks, serta menggunakan tanda kutip dua ("). \n\nError: ' +
                    e.message);
                return;
            }

            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            resultContainer.addClass('d-none');

            $.ajax({
                url: "{{ route('feeder.test.submit') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    payload: payload
                },
                success: function(response) {
                    resultContainer.removeClass('d-none');
                    jsonResult.text(JSON.stringify(response, null, 4));
                },
                error: function(xhr) {
                    resultContainer.removeClass('d-none');
                    const errorData = xhr.responseJSON ? xhr.responseJSON : {
                        message: 'Terjadi kesalahan sistem'
                    };
                    jsonResult.text(JSON.stringify(errorData, null, 4));
                },
                complete: function() {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });
    </script>
@endpush
