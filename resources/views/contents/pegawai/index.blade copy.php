@extends('layouts.main')

@push('scripts')
<script src="{{ asset('assets/js/modules/pegawai.js') }}"></script>
@endpush

@section('container')

<div class="content-wrapper pb-0" x-data="pegawaiApp()">
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
                    <div class="page-header pt-2 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper position-relative">
                                <span class="bg-gradient-primary p-2 rounded-2 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-account-tie mdi-24px text-white"></i>
                                </span>
                            </div>

                            <div>
                                <h4 class="mb-1 text-dark fw-bold">Data Pegawai</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted">Manajemen data Kepegawaian</small>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            {{-- Tombol Lanjutkan sebagai dropdown tanpa modal --}}
                            @if(isset($draftCount) && $draftCount > 0)
                            <div class="dropdown">
                                <button class="btn btn-warning fw-bold text-dark dropdown-toggle"
                                    type="button"
                                    id="dropdownDrafts"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    hx-get="{{ route('pegawais.drafts') }}"
                                    hx-trigger="click once"
                                    hx-target="#draftDropdownMenu"
                                    hx-indicator="#draftLoading">
                                    <i class="mdi mdi-history"></i> Lanjutkan ({{ $draftCount }})
                                    <span id="draftLoading" class="htmx-indicator spinner-border spinner-border-sm ms-1"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end"
                                    id="draftDropdownMenu"
                                    aria-labelledby="dropdownDrafts"
                                    style="min-width: 300px; max-height: 400px; overflow-y: auto;">
                                    {{-- Konten akan dimuat via HTMX --}}
                                    <li class="text-center p-3">
                                        <span class="spinner-border spinner-border-sm"></span>
                                        <span class="ms-2">Memuat...</span>
                                    </li>
                                </ul>
                            </div>
                            @endif
                            <button type="button" class="btn btn-primary btn-labeled"
                                hx-get="/pegawais/create"
                                hx-target="#mainModal-content"
                                hx-push-url="false">
                                <span class="btn-label">
                                    <i class="mdi mdi-plus"></i>
                                    <span class="spinner-border spinner-border-sm htmx-indicator"></span>
                                </span>
                                Tambah
                            </button>
                        </div>
                    </div>

                    <!-- Filter Component -->
                    <x-filter>
                        <!-- Per Page -->
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Per Halaman</label>
                            <select class="form-select" x-model="filters.per_page" @change="applyFilter()">
                                <option value="10">Tampilkan: 10</option>
                                <option value="25">Tampilkan: 25</option>
                                <option value="50">Tampilkan: 50</option>
                                <option value="100">Tampilkan: 100</option>
                            </select>
                        </div>

                        <!-- Status Pegawai -->
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Status Pegawai</label>
                            <select class="form-select" x-model="filters.sp" @change="applyFilter()">
                                <option value="" disabled>-- Pilih Status --</option>
                                @foreach($statusPegawais as $status)
                                <option value="{{ $status->id }}">{{ $status->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jenis Pegawai -->
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Jenis Pegawai</label>
                            <select class="form-select" x-model="filters.jp" @change="applyFilter()">
                                <option value="" disabled>-- Pilih Jenis Pegawai --</option>
                                @foreach($jenisPegawais as $jenis)
                                <option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search -->
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Cari</label>
                            <input type="text"
                                class="form-control"
                                placeholder="Nama atau Nomor Induk..."
                                x-model="filters.search"
                                @input.debounce.500ms="applyFilter()"
                                @keyup.enter="applyFilter()">
                        </div>
                    </x-filter>

                    <!-- Pegawai Table -->
                    <div id="pegawai-table"
                        hx-get="{{ route('pegawais.index') }}"
                        hx-trigger="pegawaiUpdated from:body"
                        hx-swap="innerHTML">
                        @include('contents.pegawai.partials.table')
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Pegawai (Large dengan Scroll) -->
    <x-modal id="mainModal" size="modal-lg" :scrollable="true" />

    <!-- Loading Component -->
    <x-loading />

</div>

@endsection