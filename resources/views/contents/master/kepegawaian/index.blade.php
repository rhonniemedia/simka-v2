@extends('layouts.main')

@push('scripts')
<script src="{{ asset('assets/js/modules/core-app.js') }}"></script>
@endpush

@section('container')

<div class="content-wrapper pb-0">
    <div class="page-header flex-wrap">
        <h3 class="mb-0">
            Hi, welcome back!
            <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">Your web analytics dashboard template.</span>
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Tables</a></li>
                <li class="breadcrumb-item active" aria-current="page"> Basic tables </li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">

                <div class="card-body">
                    <div class="page-header py-2 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper position-relative">
                                <span class="bg-gradient-primary p-2 rounded-2 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-account-tie mdi-24px text-white"></i>
                                </span>
                            </div>

                            <div>
                                <h4 class="mb-1 text-dark fw-bold">Master Kepegawaian</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted">Manajemen data Kepegawaian</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab-status">
                                <i class="mdi mdi-book-open-page-variant"></i> Status Kepegawaian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab-jenis">
                                <i class="mdi mdi-calendar-clock"></i>Jenis Pegawai
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab-jabatan">
                                <i class="mdi mdi-calendar-clock"></i>Jabatan Pegawai
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab-kepangkatan">
                                <i class="mdi mdi-calendar-clock"></i>Kepangkatan
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">

                        <!-- ================== TAB STATUS KEPEGAWAIAN ================== -->
                        <div class="tab-pane fade show active" id="tab-status">

                            <div id="table-container"
                                hx-get="{{ route('master.status.index') }}"
                                hx-trigger="load, statusUpdated from:body"
                                hx-swap="innerHTML">
                                <div class="text-center py-3 text-muted">
                                    Memuat data status pegawai...
                                </div>
                            </div>

                        </div>

                        <!-- ================== TAB JENIS PEGAWAI ================== -->
                        <div class="tab-pane fade" id="tab-jenis">
                            <div id="jenis-container"
                                hx-get="{{ route('master.employee-types.index') }}"
                                hx-trigger="load once, jenisPegawaiUpdated from:body"
                                hx-swap="innerHTML">
                                <div class="text-center py-3 text-muted">
                                    Memuat data jenis pegawai...
                                </div>
                            </div>
                        </div>

                        <!-- ================== TAB JABATAN PEGAWAI ================== -->
                        <div class="tab-pane fade" id="tab-jabatan">
                            <div id="jabatan-container"
                                hx-get="{{ route('master.positions.index') }}"
                                hx-trigger="load once, jabatanUpdated from:body"
                                hx-swap="innerHTML">
                                <div class="text-center py-3 text-muted">
                                    Memuat data jabatan pegawai...
                                </div>
                            </div>
                        </div>

                        <!-- ================== TAB KEPANGKATAN PEGAWAI ================== -->
                        <div class="tab-pane fade" id="tab-kepangkatan">
                            <div id="kepangkatan-container"
                                hx-trigger="load once, kepangkatanUpdated from:body"
                                hx-swap="innerHTML">
                                <div class="text-center py-3 text-muted">
                                    Memuat data kepangkatan...
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Global (Reusable untuk semua tab) -->
    <x-modal id="mainModal" :scrollable="true" />

    <!-- Loading Component -->
    <x-loading />

</div>


@endsection