<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="text-center sidebar-brand-wrapper d-flex align-items-center">
        <a class="sidebar-brand brand-logo" href="{{url('index.html')}}"><img src="{{ asset('assets/images/logo.svg') }}" alt="logo" /></a>
        <a class="sidebar-brand brand-logo-mini pl-4 pt-3" href="{{url('index.html')}}"><img src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo" /></a>
    </div>
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="{{url('#')}}" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
                    <span class="login-status online"></span>
                    <!--change to offline or busy as needed-->
                </div>
                <div class="nav-profile-text d-flex flex-column pr-3">
                    <span class="font-weight-medium mb-2">Admin
                    </span>
                    <span class="font-weight-normal">Roni Saputra</span>
                </div>
                <span class="badge badge-danger text-white ml-3 rounded">
                    A
                </span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('')}}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="{{url('#ui-master')}}" aria-expanded="false" aria-controls="ui-master">
                <i class="mdi mdi-animation menu-icon"></i>
                <span class="menu-title">Data Master</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-master">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('data-master/identitas-sekolah')}}">Organisasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('data-master/golongan')}}">Pegawai</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="{{url('#ui-pegawai')}}" aria-expanded="false" aria-controls="ui-pegawai">
                <i class="mdi mdi-account-group menu-icon"></i>
                <span class="menu-title">Pegawai</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-pegawai">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('employees/data')}}">Data Pegawai</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('employees/mutations')}}">Mutasi</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('kepegawaian/pangkat')}}">
                <i class="mdi mdi mdi-file-chart menu-icon"></i>
                <span class="menu-title">Dokumen</span>
            </a>
        </li>
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Kepegawaian</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('kepegawaian/pangkat')}}">
                <i class="mdi mdi-account-star menu-icon"></i>
                <span class="menu-title">Kepangkatan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('kepegawaian/berkala')}}">
                <i class="mdi mdi-buffer menu-icon"></i>
                <span class="menu-title">Berkala</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('career/retirements')}}">
                <i class="mdi mdi-star-off menu-icon"></i>
                <span class="menu-title">Pensiun</span>
            </a>
        </li>

        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Pendukung</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('kepegawaian/pendidikan')}}">
                <i class="mdi mdi-school menu-icon"></i>
                <span class="menu-title">Pendidikan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('kepegawaian/keluarga')}}">
                <i class="mdi mdi-human-male-female menu-icon"></i>
                <span class="menu-title">Keluarga</span>
            </a>
        </li>

        @can('admin')
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Laporan</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="{{url('#ui-report')}}" aria-expanded="false" aria-controls="ui-report">
                <i class="mdi mdi mdi-cloud-print menu-icon"></i>
                <span class="menu-title">Cetak Laporan</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-report">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('laporan/duk')}}" target="_blank">Urut Kepangkatan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('laporan/tunjangan')}}">Tunjangan (KP4)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('#')}}">Daftar Pegawai</a>
                    </li>
                </ul>
            </div>
        </li>
        @endcan
        @can('admin')
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Manajemen User</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('user-manajemen/user')}}">
                <i class="mdi mdi-clipboard-account menu-icon"></i>
                <span class="menu-title">Daftar User</span>
            </a>
        </li>
        @endcan
    </ul>
</nav>