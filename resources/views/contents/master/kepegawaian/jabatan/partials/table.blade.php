<div class="row mb-3 align-items-center g-2">
    <!-- Kiri -->
    <div class="col-12 col-md-6">
        <div class="d-flex align-items-center gap-2">
            <span>Show</span>
            <select class="form-select form-select"
                style="width:80px;"
                name="per_page"
                hx-get="{{ route('master.positions.index') }}"
                hx-target="#jabatan-container"
                hx-include="[name='search']">
                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
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
                hx-get="{{ route('master.positions.index') }}"
                hx-target="#jabatan-container"
                hx-swap="innerHTML"
                hx-trigger="keyup changed delay:500ms, search">

            <button type="button"
                class="btn btn-input-addon"
                hx-get="{{ route('master.positions.create') }}"
                hx-target="#mainModal-content"
                hx-swap="innerHTML"
                data-bs-toggle="modal"
                data-bs-target="#mainModal">
                <i class="mdi mdi-plus"></i>
                <span class="spinner-border spinner-border-sm htmx-indicator-custom"></span>
            </button>
        </div>
    </div>
</div>

<div class="table-responsive">
    @if($jabatanPegawais->isEmpty())
    <div class="alert alert-info text-center">
        <i class="mdi mdi-alert-circle-outline"></i>
        @if(request('search'))
        Tidak ada data yang cocok dengan pencarian "{{ request('search') }}".
        @else
        Tidak ada data status pegawai yang ditemukan.
        @endif
    </div>
    @else
    <table class="table table-hover align-middle">
        <thead class="bg-light">
            <tr>
                <th style="width: 40%;">
                    <p class="mb-0"> Nama </p>
                    <small>Jabatan Pegawai</small>
                </th>
                <th style="width: 40%;">
                    <p class="mb-0"> Status </p>
                    <small>Jabatan Pegawai</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0"> Aksi </p>
                    <small>Edit | Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($jabatanPegawais as $jp)
            <tr>
                <td>
                    <p class="mb-0 font-weight-medium">{{ $jp->nama }}</p>
                    <small>{{ $jp->kode ?? '-' }}</small>
                </td>

                <td>
                    <span class="badge {{ $jp->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                        {{ strtoupper($jp->status) }}
                    </span>
                </td>

                <td>
                    <button type="button"
                        class="btn btn-sm btn-outline-info btn-icon-only"
                        title="Edit"
                        hx-get="{{ route('master.positions.edit', $jp->id) }}"
                        hx-target="#mainModal-content"
                        hx-swap="innerHTML"
                        data-bs-toggle="modal"
                        data-bs-target="#mainModal">
                        <i class="mdi mdi-pencil"></i>
                        <span class="spinner-border spinner-border-sm htmx-indicator"></span>
                    </button>
                    <button type="button"
                        class="btn btn-sm btn-outline-danger"
                        hx-delete="{{ route('master.positions.destroy', $jp->id) }}"
                        hx-target="closest tr"
                        hx-swap="outerHTML swap:1s"
                        hx-trigger="confirmed"
                        @click="confirmDelete($el, '{{ addslashes($jp->nama) }}')">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@if(!$jabatanPegawais->isEmpty())
<div class="mt-3">
    {{ $jabatanPegawais->appends(['search' => request('search'), 'per_page' => request('per_page')])->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
@endif