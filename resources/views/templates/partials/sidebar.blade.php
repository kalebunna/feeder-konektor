<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Main</li>

                <li>
                    <a href="{{ route('prodi.index') }}">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                <li class="menu-title mt-2" data-key="t-data">Master Data</li>

                <li>
                    <a href="{{ route('prodi.index') }}">
                        <i data-feather="briefcase"></i>
                        <span data-key="t-prodi">Program Studi</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('profil-pt.index') }}">
                        <i data-feather="info"></i>
                        <span data-key="t-profil-pt">Profil PT</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('tahun-ajaran.index') }}">
                        <i data-feather="calendar"></i>
                        <span data-key="t-tahun-ajaran">Tahun Ajaran</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('semester.index') }}">
                        <i data-feather="layers"></i>
                        <span data-key="t-semester">Semester</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('biodata-mahasiswa.index') }}">
                        <i data-feather="users"></i>
                        <span data-key="t-mahasiswa-biodata">Biodata Mahasiswa</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('mahasiswa.index') }}">
                        <i data-feather="list"></i>
                        <span data-key="t-mahasiswa-daftar">Daftar Mahasiswa</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('matakuliah-lokal.index') }}">
                        <i data-feather="book-open"></i>
                        <span data-key="t-matakuliah-lokal">Mata Kuliah Lokal</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('kurikulum.index') }}">
                        <i data-feather="grid"></i>
                        <span data-key="t-kurikulum">Kurikulum</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="book"></i>
                        <span data-key="t-kelas-kuliah">Kelas Kuliah</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('kelas-kuliah.index') }}" data-key="t-list-kelas">Daftar Kelas</a></li>
                        <li><a href="{{ route('kelas-kuliah.create') }}" data-key="t-generate-kelas">Generate Kelas</a>
                        </li>
                    </ul>
                </li>

                <li class="menu-title mt-2" data-key="t-feeder">Integrasi Feeder</li>

                <li>
                    <a href="{{ route('feeder.test') }}">
                        <i data-feather="database"></i>
                        <span data-key="t-json-explorer">JSON Explorer</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('reference.index') }}">
                        <i data-feather="refresh-cw"></i>
                        <span data-key="t-sync">Sinkronisasi</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('import-mahasiswa.index') }}">
                        <i data-feather="upload"></i>
                        <span data-key="t-import-mahasiswa">Import Mahasiswa</span>
                    </a>
                </li>
            </ul>

            <div class="card sidebar-alert border-0 text-center mx-4 mb-0 mt-5">
                <div class="card-body">
                    <img src="{{ asset('templates/assets/images/giftbox.png') }}" alt="">
                    <div class="mt-4">
                        <h5 class="alertcard-title font-size-16">Unlimited Access</h5>
                        <p class="font-size-13">Upgrade your plan from a Free trial, to select ‘Business Plan’.
                        </p>
                        <a href="#!" class="btn btn-primary mt-2">Upgrade Now</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
