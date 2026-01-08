<div class="modal-content">
    <form hx-post="{{ $mutation->exists ? route('employees.mutations.update', $mutation->id) : route('employees.mutations.store') }}"
        hx-encoding="multipart/form-data"
        hx-indicator="#loading"
        x-data="{
            selectedSlug: '{{ $mutation->peg_slug ?? '' }}'
        }">

        @if($mutation->exists)
        @method('PUT')
        @endif

        <div class="modal-header">
            <h5 class="modal-title">{{ $mutation->exists ? 'Edit Mutasi' : 'Tambah Mutasi' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <div class="mb-3">
                <label for="searchPegawai" class="form-label">Nama Pegawai</label>
                <input
                    class="form-control"
                    list="datalistOptions"
                    id="searchPegawai"
                    placeholder="Cari nama pegawai..."
                    autocomplete="off"
                    required
                    x-on:input="
                        const val = $event.target.value;
                        const option = document.querySelector('#datalistOptions option[value=\'' + val + '\']');
                        selectedSlug = option ? option.dataset.id : '';
                    "
                    value="{{ $mutation->nama ?? '' }}"
                    {{ $mutation->exists ? 'disabled' : '' }}>

                <input type="hidden" name="peg_slug" x-model="selectedSlug">

                <datalist id="datalistOptions">
                    @foreach ($daftarPegawais as $dataPegawai)
                    <option value="{{ $dataPegawai->nama }}" data-id="{{ $dataPegawai->peg_slug }}"></option>
                    @endforeach
                </datalist>

                <div class="form-text text-info" x-show="selectedSlug" x-cloak>
                    <i class="mdi mdi-check-circle"></i> Pegawai terverifikasi.
                </div>

                <div class="form-text text-danger" x-show="!selectedSlug && $el.previousElementSibling.previousElementSibling.value" x-cloak>
                    <i class="mdi mdi-alert-circle"></i> Pegawai tidak ditemukan. Pilih dari daftar.
                </div>
            </div>

            <!-- Status Mutasi -->
            <div class="mb-3">
                <label for="status" class="form-label">Status Mutasi <span class="text-danger">*</span></label>
                <select class="form-select" name="status" id="status" required>
                    <option value="" disabled {{ !$mutation->exists ? 'selected' : '' }}>-- Pilih Status --</option>
                    <option value="pindah" {{ ($mutation->status ?? '') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                    <option value="mundur" {{ ($mutation->status ?? '') == 'mundur' ? 'selected' : '' }}>Mundur</option>
                    <option value="meninggal" {{ ($mutation->status ?? '') == 'meninggal' ? 'selected' : '' }}>Meninggal</option>
                </select>
            </div>

            <!-- TMT Status -->
            <div class="mb-3">
                <label for="tmt_status" class="form-label">TMT Status <span class="text-danger">*</span></label>
                <input
                    type="date"
                    class="form-control"
                    name="tmt_status"
                    id="tmt_status"
                    value="{{ $mutation->tmt_status ?? '' }}"
                    required>
                <small class="form-text text-muted">Tanggal Mulai Tugas status baru</small>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-labeled" data-bs-dismiss="modal">
                <span class="btn-label"><i class="mdi mdi-close"></i></span>
                Batal
            </button>
            <button type="submit" class="btn btn-primary btn-labeled" :disabled="!selectedSlug">
                <span class="btn-label">
                    <i class="mdi mdi-floppy save-icon"></i>
                    <span class="spinner-border spinner-border-sm htmx-indicator"></span>
                </span>
                Simpan & Lanjut
            </button>
        </div>
    </form>
</div>