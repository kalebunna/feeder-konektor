@extends('templates.layout')

@section('title', 'Manajemen API Token (Sanctum)')
@section('page-title', 'Manajemen API Token & Dokumentasi')

@section('content')
    <div class="row">
        <!-- Stat Cards -->
        <div class="col-md-4">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate font-size-13">Total Token Aktif</span>
                            <h4 class="mb-0 text-primary">
                                <span>{{ $tokens->count() }}</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                <i class="bx bx-key font-size-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate font-size-13">Tipe Autentikasi</span>
                            <h4 class="mb-0 text-success">
                                <span>Bearer Token (Sanctum)</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">
                                <i class="bx bx-shield-quarter font-size-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate font-size-13">Rate Limiting</span>
                            <h4 class="mb-0 text-info">
                                <span>60 Req / Menit</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center">
                                <i class="bx bx-tachometer font-size-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title">Daftar API Token</h4>
                        <p class="card-title-desc mb-0">Kelola izin akses API untuk aplikasi luar (Portal Kampus, Mobile Apps, dll).</p>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateToken">
                        <i class="bx bx-plus-circle me-1 font-size-16 align-middle"></i> Buat Token Baru
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%" class="text-center">No</th>
                                    <th>Nama Token / Klien</th>
                                    <th>Hak Akses (Abilities)</th>
                                    <th>Dibuat Pada</th>
                                    <th>Terakhir Digunakan</th>
                                    <th>Kadaluarsa</th>
                                    <th style="width: 10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tokens as $index => $t)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong class="text-dark">{{ $t->name }}</strong>
                                        </td>
                                        <td>
                                            @if(empty($t->abilities) || in_array('*', $t->abilities))
                                                <span class="badge bg-primary">Akses Penuh (*)</span>
                                            @else
                                                @foreach($t->abilities as $ability)
                                                    <span class="badge bg-secondary me-1">{{ $ability }}</span>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>{{ $t->created_at ? $t->created_at->format('d M Y H:i') : '-' }}</td>
                                        <td>
                                            @if($t->last_used_at)
                                                <span class="text-success">{{ $t->last_used_at->diffForHumans() }}</span>
                                            @else
                                                <span class="text-muted">Belum pernah</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($t->expires_at)
                                                <span class="{{ $t->expires_at->isPast() ? 'text-danger fw-bold' : 'text-muted' }}">
                                                    {{ $t->expires_at->format('d M Y') }}
                                                </span>
                                            @else
                                                <span class="badge bg-success-subtle text-success">Selamanya</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-token" data-id="{{ $t->id }}" data-name="{{ $t->name }}">
                                                <i class="bx bx-trash font-size-14 align-middle"></i> Cabut
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bx bx-key font-size-24 d-block mb-1"></i>
                                            Belum ada API Token yang dibuat. Klik tombol <strong>"Buat Token Baru"</strong> di atas untuk membuat token pertama Anda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- API Documentation Section -->
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bx bx-book-open me-2 text-primary"></i>Panduan & Dokumentasi Endpoint API</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Semua request API wajib menyertakan header <code>Authorization: Bearer &lt;TOKEN_ANDA&gt;</code> dan <code>Accept: application/json</code>.</p>

                    <div class="accordion" id="accordionApiDocs">
                        <!-- Endpoint 1: Biodata Mahasiswa -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingBiodata">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBiodata">
                                    <span class="badge bg-success me-2 font-size-12">GET</span>
                                    <code>/api/v1/biodata-mahasiswa</code> &nbsp;&mdash;&nbsp; Data Lengkap Biodata & Registrasi Mahasiswa (Neo Feeder ID)
                                </button>
                            </h2>
                            <div id="collapseBiodata" class="accordion-collapse collapse show" data-bs-parent="#accordionApiDocs">
                                <div class="accordion-body">
                                    <div class="alert alert-info py-2">
                                        <strong>Wajib:</strong> Endpoint ini mewajibkan 2 parameter: <code>id_prodi</code> (UUID Prodi Feeder) dan <code>id_periode</code> (ID Periode Masuk, misal <code>20231</code>).
                                    </div>
                                    <h6>Parameter Query (Wajib):</h6>
                                    <ul>
                                        <li><code>id_prodi</code> <span class="badge bg-danger">Wajib</span> : UUID Program Studi dari Neo Feeder</li>
                                        <li><code>id_periode</code> <span class="badge bg-danger">Wajib</span> : ID Periode Masuk Neo Feeder (contoh: <code>20231</code> atau <code>20241</code>)</li>
                                    </ul>
                                    <h6>Contoh cURL:</h6>
                                    <pre class="bg-dark text-white p-3 rounded"><code>curl -X GET "{{ url('/api/v1/biodata-mahasiswa') }}?id_prodi=69a210b6-22a3-4b86-b12d-96d6008b664b&id_periode=20231" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"</code></pre>
                                </div>
                            </div>
                        </div>

                        <!-- Endpoint 2: Semester -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSemester">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSemester">
                                    <span class="badge bg-success me-2 font-size-12">GET</span>
                                    <code>/api/v1/semesters</code> &nbsp;&mdash;&nbsp; Data Periode / Semester Perkuliahan
                                </button>
                            </h2>
                            <div id="collapseSemester" class="accordion-collapse collapse" data-bs-parent="#accordionApiDocs">
                                <div class="accordion-body">
                                    <h6>Parameter Query (Opsional):</h6>
                                    <ul>
                                        <li><code>aktif=1</code> : Hanya mengambil semester yang sedang aktif saat ini</li>
                                        <li><code>all=1</code> : Mengambil seluruh data tanpa pagination</li>
                                    </ul>
                                    <h6>Contoh cURL:</h6>
                                    <pre class="bg-dark text-white p-3 rounded"><code>curl -X GET "{{ url('/api/v1/semesters') }}?aktif=1" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"</code></pre>
                                </div>
                            </div>
                        </div>

                        <!-- Endpoint 3: Jurusan / Prodi -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingJurusan">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseJurusan">
                                    <span class="badge bg-success me-2 font-size-12">GET</span>
                                    <code>/api/v1/jurusans</code> &nbsp;&mdash;&nbsp; Data Program Studi & UUID Feeder (<code>id_prodi</code>)
                                </button>
                            </h2>
                            <div id="collapseJurusan" class="accordion-collapse collapse" data-bs-parent="#accordionApiDocs">
                                <div class="accordion-body">
                                    <h6>Contoh cURL:</h6>
                                    <pre class="bg-dark text-white p-3 rounded"><code>curl -X GET "{{ url('/api/v1/jurusans') }}" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"</code></pre>
                                </div>
                            </div>
                        </div>

                        <!-- Endpoint 4: Nilai Perkuliahan -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingNilai">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNilai">
                                    <span class="badge bg-success me-2 font-size-12">GET</span>
                                    <code>/api/v1/nilai-perkuliahan</code> &nbsp;&mdash;&nbsp; Tarik Data Nilai Mahasiswa per Prodi & Semester (Ganjil/Genap)
                                </button>
                            </h2>
                            <div id="collapseNilai" class="accordion-collapse collapse" data-bs-parent="#accordionApiDocs">
                                <div class="accordion-body">
                                    <div class="alert alert-info py-2">
                                        <strong>Wajib:</strong> Endpoint ini mewajibkan 2 parameter: <code>id_prodi</code> (UUID Prodi Feeder) dan <code>id_semester</code> (ID Semester, misal <code>20241</code> untuk Ganjil, <code>20242</code> untuk Genap).
                                    </div>
                                    <h6>Parameter Query (Wajib):</h6>
                                    <ul>
                                        <li><code>id_prodi</code> <span class="badge bg-danger">Wajib</span> : UUID Program Studi dari Neo Feeder</li>
                                        <li><code>id_semester</code> <span class="badge bg-danger">Wajib</span> : ID Semester Neo Feeder (contoh: <code>20241</code>)</li>
                                    </ul>
                                    <h6>Contoh cURL:</h6>
                                    <pre class="bg-dark text-white p-3 rounded"><code>curl -X GET "{{ url('/api/v1/nilai-perkuliahan') }}?id_prodi=69a210b6-22a3-4b86-b12d-96d6008b664b&id_semester=20241" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create Token -->
    <div class="modal fade" id="modalCreateToken" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formCreateToken">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-key me-1 text-primary"></i> Buat API Token Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tokenName" class="form-label">Nama Token / Klien <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tokenName" name="name" placeholder="Contoh: Portal Akademik / Aplikasi Mobile" required>
                            <small class="text-muted">Gunakan nama yang mudah dikenali untuk mencatat siapa pemegang token ini.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Masa Berlaku (Expired)</label>
                            <select class="form-select" name="expires_in" id="expiresIn">
                                <option value="">Selamanya (Tidak Pernah Kadaluarsa)</option>
                                <option value="30">30 Hari</option>
                                <option value="90">90 Hari (3 Bulan)</option>
                                <option value="365">1 Tahun</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Hak Akses (Abilities)</label>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="abilities[]" value="*" id="abilityAll" checked>
                                <label class="form-check-label fw-bold" for="abilityAll">Akses Penuh (*)</label>
                            </div>
                            <div class="form-check mb-1">
                                <input class="form-check-input ability-item" type="checkbox" name="abilities[]" value="mahasiswa:read" id="abilityMhs">
                                <label class="form-check-label" for="abilityMhs">Baca Data Mahasiswa (<code>mahasiswa:read</code>)</label>
                            </div>
                            <div class="form-check mb-1">
                                <input class="form-check-input ability-item" type="checkbox" name="abilities[]" value="semester:read" id="abilitySemester">
                                <label class="form-check-label" for="abilitySemester">Baca Data Semester (<code>semester:read</code>)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input ability-item" type="checkbox" name="abilities[]" value="jurusan:read" id="abilityJurusan">
                                <label class="form-check-label" for="abilityJurusan">Baca Data Jurusan (<code>jurusan:read</code>)</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitToken">
                            <span class="spinner-border spinner-border-sm d-none" id="spinnerCreate"></span> Buat Token
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Token Result (Copy Token) -->
    <div class="modal fade" id="modalTokenResult" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content border-success">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bx bx-check-shield me-1"></i> API Token Berhasil Dibuat!</h5>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <i class="bx bx-error align-middle me-1"></i>
                        <strong>PENTING:</strong> Salin token Anda sekarang! Demi keamanan, token ini <strong>TIDAK AKAN DITAMPILKAN LAGI</strong> setelah modal ini ditutup.
                    </div>
                    <label class="form-label">Bearer Token Anda:</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control font-monospace bg-light" id="generatedTokenText" readonly>
                        <button class="btn btn-primary" type="button" id="btnCopyToken">
                            <i class="bx bx-copy align-middle me-1"></i> Salin
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="window.location.reload()">Saya Sudah Menyimpannya</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Handle ability checkbox toggle
            $('#abilityAll').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.ability-item').prop('checked', false);
                }
            });
            $('.ability-item').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#abilityAll').prop('checked', false);
                }
            });

            // Handle create token form submit
            $('#formCreateToken').on('submit', function(e) {
                e.preventDefault();

                const btn = $('#btnSubmitToken');
                const spinner = $('#spinnerCreate');

                btn.prop('disabled', true);
                spinner.removeClass('d-none');

                $.ajax({
                    url: "{{ route('api-tokens.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#modalCreateToken').modal('hide');
                        $('#formCreateToken')[0].reset();

                        $('#generatedTokenText').val(res.token);
                        $('#modalTokenResult').modal('show');
                    },
                    error: function(xhr) {
                        let msg = 'Gagal membuat token.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: msg
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                        spinner.addClass('d-none');
                    }
                });
            });

            // Copy token button
            $('#btnCopyToken').on('click', function() {
                const tokenInput = document.getElementById('generatedTokenText');
                tokenInput.select();
                tokenInput.setSelectionRange(0, 99999); // Mobile
                navigator.clipboard.writeText(tokenInput.value);

                $(this).html('<i class="bx bx-check align-middle me-1"></i> Tersalin!').removeClass('btn-primary').addClass('btn-success');
                setTimeout(() => {
                    $(this).html('<i class="bx bx-copy align-middle me-1"></i> Salin').removeClass('btn-success').addClass('btn-primary');
                }, 2000);
            });

            // Delete / Revoke Token
            $('.btn-delete-token').on('click', function() {
                const tokenId = $(this).data('id');
                const tokenName = $(this).data('name');

                Swal.fire({
                    title: 'Cabut Izin Token?',
                    html: `Apakah Anda yakin ingin menghapus token <strong>"${tokenName}"</strong>?<br><small class="text-danger">Aplikasi yang menggunakan token ini tidak akan bisa mengakses API lagi!</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#74788d',
                    confirmButtonText: 'Ya, Cabut Akses!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/api-tokens') }}/" + tokenId,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat menghapus token.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
