<div class="modal-content">
    <form hx-post="{{ $retirement->exists ? route('career.retirements.update', $retirement->id) : route('career.retirements.store') }}"
        hx-encoding="multipart/form-data"
        x-data="{
            selectedSlug: '{{ $retirement->peg_slug ?? '' }}'
        }">

        @if($retirement->exists)
        @method('PUT')
        @endif

        <div class="modal-header">
            <h5 class="modal-title">{{ $retirement->exists ? 'Edit Mutasi' : 'Tambah Mutasi' }}</h5>
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
                    value="{{ $retirement->nama ?? '' }}"
                    {{ $retirement->exists ? 'disabled' : '' }}>

                <input type="hidden" name="peg_slug" x-model="selectedSlug">

                <datalist id="datalistOptions">
                    @foreach ($daftarPegawais as $dataPegawai)
                    <option value="{{ $dataPegawai->nama }}" data-id="{{ $dataPegawai->peg_slug }}"></option>
                    @endforeach
                </datalist>

                <div class="form-text text-info" x-show="selectedSlug" x-cloak>
                    <small>Pegawai terverifikasi.</small>
                </div>

                <div class="form-text text-danger" x-show="!selectedSlug && $el.previousElementSibling.previousElementSibling.value" x-cloak>
                    <i class="mdi mdi-alert-circle"></i> Pegawai tidak ditemukan. Pilih dari daftar.
                </div>
            </div>

            <!-- Status retirement -->
            <div class="mb-3">
                <label for="status" class="form-label">Status retirement <span class="text-danger">*</span></label>
                <select class="form-select" name="status" id="status" required>
                    <option value="" disabled {{ !$retirement->exists ? 'selected' : '' }}>-- Pilih Status --</option>
                    <option value="pensiun" {{ ($retirement->status ?? '') == 'pensiun' ? 'selected' : '' }}>Pensiun</option>
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
                    value="{{ $retirement->tmt_status ?? '' }}"
                    required>
                <small class="text-muted">Terhitung mulai tanggal status baru</small>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-labeled" data-bs-dismiss="modal">
                <span class="btn-label"><i class="mdi mdi-close"></i></span>
                Batal
            </button>
            <button type="submit" class="btn btn-primary btn-labeled">
                <span class="btn-label">
                    <i class="mdi mdi-floppy save-icon"></i>
                    <span class="spinner-border spinner-border-sm htmx-indicator"></span>
                </span>
                Simpan
            </button>
        </div>
    </form>
</div>