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
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📦 Data Produk</h5>
                    <button class="btn btn-primary"
                        hx-get="/pegawais/create"
                        hx-target="#mainModal-content"
                        hx-push-url="false">
                        + Tambah Produk
                    </button>
                </div>
                <div class="card-body">
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
                                @foreach($statusPegawais as $status)
                                <option value="{{ $status->id }}">{{ $status->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jenis Pegawai -->
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Jenis Pegawai</label>
                            <select class="form-select" x-model="filters.jp" @change="applyFilter()">
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
                                x-model="filters.search">
                        </div>
                    </x-filter>

                    <!-- Pegawai Table -->
                    <div id="pegawai-table">
                        @include('contents.pegawai.partials.table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Product (Large dengan Scroll) -->
    <x-modal id="mainModal" size="modal-lg" :scrollable="true" />

    <!-- Loading Component -->
    <x-loading />

</div>

@endsection