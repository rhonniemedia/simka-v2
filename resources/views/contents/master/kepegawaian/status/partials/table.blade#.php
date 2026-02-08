<div class="row mb-3 align-items-center g-2">
    <!-- Kiri -->
    <div class="col-12 col-md-6">
        <div class="d-flex align-items-center gap-2">
            <span>Show</span>
            <select class="form-select form-select" style="width:80px;">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <span>entries</span>
        </div>
    </div>

    <!-- Kanan -->
    <div class="col-12 col-md-6">
        <div class="d-flex w-100 gap-2 justify-content-md-end">

            <input type="search"
                class="form-control flex-grow-1"
                placeholder="Cari..."
                name="search"
                id="search-input"
                value="{{ request('search') }}"
                style="max-width:250px;"
                hx-get="{{ route('master.status.index') }}"
                hx-target="#table-container"
                hx-swap="innerHTML"
                hx-include="[name='per_page']"
                hx-trigger="keyup changed delay:500ms, search">

            <button type="button"
                class="btn btn-input-addon"
                hx-get="{{ route('master.status.create') }}"
                hx-target="#mainModal-content"
                hx-on:click="document.getElementById('mainModal-content').innerHTML = ''"
                data-bs-toggle="modal"
                data-bs-target="#mainModal">
                <i class="mdi mdi-plus"></i>
                <span class="spinner-border spinner-border-sm htmx-indicator-custom"></span>
            </button>
        </div>
    </div>
</div>

<div class="table-responsive">
    @if($statusPegawais->isEmpty())
    <div class="alert alert-info text-center">
        <i class="mdi mdi-alert-circle-outline"></i> Tidak ada data status pegawai yang ditemukan.
    </div>
    @else
    <table class="table table-hover align-middle">
        <thead class="bg-light">
            <tr>
                <th style="width: 40%;">
                    <p class="mb-0"> Nama </p>
                    <small>Status Kepegawaian</small>
                </th>
                <th style="width: 40%;">
                    <p class="mb-0"> Status </p>
                    <small>Jenis Kepegawaian</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0"> Aksi </p>
                    <small>Edit | Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($statusPegawais as $sp)
            <tr>
                <td>
                    <p class="mb-0 font-weight-medium">{{ $sp->nama }}</p>
                    <small>{{ $sp->alias ?? '-' }}</small>
                </td>

                <td>
                    <span class="badge {{ $sp->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                        {{ strtoupper($sp->status) }}
                    </span>
                </td>

                <td>
                    <button type="button"
                        class="btn btn-sm btn-outline-info btn-icon-only"
                        title="Edit"
                        hx-get="{{ route('master.status.edit', $sp->id) }}"
                        hx-target="#mainModal-content"
                        hx-swap="innerHTML"
                        @click="document.getElementById('mainModal-content').innerHTML = ''"
                        data-bs-toggle="modal"
                        data-bs-target="#mainModal">
                        <i class="mdi mdi-pencil"></i>
                        <span class="spinner-border spinner-border-sm htmx-indicator"></span>
                    </button>
                    <button type="button"
                        class="btn btn-sm btn-outline-danger"
                        hx-delete="{{ route('master.status.destroy', $sp->id) }}"
                        hx-target="closest tr"
                        hx-swap="outerHTML swap:1s"
                        hx-trigger="confirmed" {{-- 1. TAHAN, tunggu event 'confirmed' --}}
                        @click="confirmDelete($el, '{{ addslashes($sp->nama) }}')"> {{-- 2. Kirim $el --}}
                        <i class="mdi mdi-delete"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@if(!$statusPegawais->isEmpty())
<div class="mt-3">
    {{ $statusPegawais->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
@endif