<div class="modal-content">
    <form hx-post="{{ $status->exists ? route('master.resources.update', $status->id) : route('master.resources.store') }}"
        hx-swap="innerHTML">
        {{-- ✅ hx-swap="none" karena kita handle response via event --}}

        @if($status->exists)
        @method('PUT')
        @endif

        <div class="modal-header">
            <h5 class="modal-title font-weight-bold">
                {{ $status->exists ? 'Edit Status Kepegawaian' : 'Tambah Status Kepegawaian' }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            {{-- Nama Status --}}
            <div class="mb-3">
                <label class="form-label">
                    Nama Status <span class="text-danger">*</span>
                </label>
                <input type="text"
                    name="nama"
                    class="form-control @error('nama') is-invalid @enderror"
                    value="{{ old('nama', $status->nama) }}"
                    placeholder="Contoh: PNS"

                    autofocus>
                @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Nama singkat status kepegawaian</small>
            </div>

            {{-- Alias --}}
            <div class="mb-3">
                <label class="form-label">
                    Alias <span class="text-danger">*</span>
                </label>
                <input type="text"
                    name="alias"
                    class="form-control @error('alias') is-invalid @enderror"
                    value="{{ old('alias', $status->alias) }}"
                    placeholder="Contoh: Pegawai Negeri Sipil">
                @error('alias')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Nama lengkap status kepegawaian</small>
            </div>

            {{-- Status Keaktifan --}}
            <div class="mb-3">
                <label class="form-label"> Status Keaktifan <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="" {{ old('status', $status->status ?? '') == '' ? 'selected' : '' }}> -- Pilih Status -- </option>
                    <option value="aktif" {{ old('status', $status->status) == 'aktif' ? 'selected' : '' }}> Aktif </option>
                    <option value="arsip" {{ old('status', $status->status) == 'arsip' ? 'selected' : '' }}> Arsip </option>
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Status aktif akan ditampilkan di form input</small>
            </div>

            {{-- Info jika Edit --}}
            @if($status->exists)
            <div class="alert alert-info fade show d-flex align-items-center mb-0" role="alert">
                {{-- Ikon --}}
                <i class="mdi mdi-information-outline me-2" style="font-size: 1.2rem"></i>

                {{-- Teks --}}
                <div>
                    <small><strong>Mode Edit:</strong> Anda sedang mengubah data "{{ $status->nama }}"</small>
                </div>

                {{-- Tombol Close --}}
                {{-- ms-auto: Dorong ke kanan mentok --}}
                {{-- Karena induknya d-flex align-items-center, tombol ini otomatis vertikal tengah --}}
                <button type="button"
                    class="btn-close ms-auto"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                    style="font-size: 0.75rem;"> {{-- Ukuran default adalah 1rem, ini diperkecil ke 0.75 --}}
                </button>
            </div>
            @endif
        </div>

        <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary btn-labeled" data-bs-dismiss="modal">
                <span class="btn-label"><i class="mdi mdi-close"></i></span>
                Batal
            </button>
            <button type="submit" class="btn btn-primary btn-labeled">
                <span class="btn-label">
                    <i class="mdi mdi-content-save save-icon"></i>
                    <span class="spinner-border spinner-border-sm htmx-indicator"></span>
                </span>
                {{ $status->exists ? 'Update' : 'Simpan' }}
            </button>
        </div>
    </form>
</div>

{{-- ✅ Script untuk auto-focus input pertama --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const firstInput = document.querySelector('input[name="nama"]');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 300);
        }
    });
</script>