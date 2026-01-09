@extends('layouts.main')

@push('scripts')
<script src="{{ asset('assets/js/modules/pensiun.js') }}"></script>
@endpush

@section('container')

<div class="content-wrapper pb-0" x-data="pensiunApp()">
    <div class="page-header flex-wrap">
        <h3 class="mb-0">
            Pensiun
            <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">Data Pensiun Pegawai</span>
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Pegawai</li>
                <li class="breadcrumb-item active" aria-current="page"> Pensiun </li>
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
                                    <i class="mdi mdi-star-off mdi-24px text-white"></i>
                                </span>
                            </div>

                            <div>
                                <h4 class="mb-1 text-dark fw-bold">Pensiun Pegawai</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted">Manajemen data Pensiun Pegawai</small>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary btn-labeled"
                                hx-get="/career/retirements/create"
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

                        <!-- Status Mutasi -->
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Status Mutasi</label>
                            <select class="form-select" x-model="filters.mutasi" @change="applyFilter()">
                                <option value="" disabled>-- Status Mutasi --</option>
                                <option value="pindah">Pindah Instansi</option>
                                <option value="mundur">Mengundurkan Diri</option>
                                <option value="meninggal">Meninggal Dunia</option>
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

                    <!-- Mutasi Table -->
                    <div id="pensiun-table"
                        hx-get="{{ route('career.retirements.index') }}"
                        hx-trigger="pensiunUpdated from:body"
                        hx-swap="innerHTML">
                        @include('contents.pensiun.partials.table')
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Component -->
    <x-modal id="mainModal" />

    <!-- Loading Component -->
    <x-loading />

</div>

@endsection