@extends('layouts.main')

@push('scripts')
<script src="{{ asset('assets/js/modules/product.js') }}"></script>
@endpush

@section('container')

<div class="content-wrapper pb-0" x-data="productApp()">
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
                        hx-get="/products/create"
                        hx-target="#modal-content"
                        hx-push-url="false">
                        + Tambah Produk
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="filter-section">
                        <form id="filter-form" @submit.prevent="applyFilter()">
                            <div class="row g-3">
                                <!-- Search -->
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">🔍 Cari Produk</label>
                                    <input type="text"
                                        class="form-control"
                                        placeholder="Nama atau SKU..."
                                        x-model="filters.search">
                                </div>

                                <!-- Min Price -->
                                <div class="col-md-2">
                                    <label class="form-label small text-muted">Harga Min</label>
                                    <input type="number"
                                        class="form-control"
                                        placeholder="0"
                                        x-model="filters.min_price">
                                </div>

                                <!-- Max Price -->
                                <div class="col-md-2">
                                    <label class="form-label small text-muted">Harga Max</label>
                                    <input type="number"
                                        class="form-control"
                                        placeholder="999999"
                                        x-model="filters.max_price">
                                </div>

                                <!-- Per Page -->
                                <div class="col-md-2">
                                    <label class="form-label small text-muted">Per Halaman</label>
                                    <select class="form-select" x-model="filters.per_page" @change="applyFilter()">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>

                                <!-- Actions -->
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100 me-1">
                                        Filter
                                    </button>
                                    <button type="button" class="btn btn-secondary" @click="resetFilter()">
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Product Table -->
                    <div id="product-table">
                        @include('partials.table')
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