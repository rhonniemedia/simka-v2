<form hx-put="{{ route('pegawais.finalize', isset($pegawai) ? $pegawai->id : '') }}"
    hx-target="#step-content-placeholder">
    @method('PUT')
    @csrf

    <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
        <div class="row">
            @if(isset($pegawai))
            <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
            @endif

            <div class="col-md-6 mb-3">
                <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                <input type="tel" name="telepon" class="form-control @error('telepon') is-invalid @enderror"
                    placeholder="Contoh: 081234567890" value="{{ old('telepon', $pegawai->telepon ?? '') }}">
                @error('telepon') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Nomor Telepon Alternatif <span class="text-muted">(Opsional)</span></label>
                <input type="tel" name="telepon_alt" class="form-control @error('telepon_alt') is-invalid @enderror"
                    placeholder="Nomor cadangan" value="{{ old('telepon_alt', $pegawai->telepon_alt ?? '') }}">
                @error('telepon_alt') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Alamat Surel (Email) <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="nama@email.com" value="{{ old('email', $pegawai->email ?? '') }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Alamat Surel Alternatif <span class="text-muted">(Opsional)</span></label>
                <input type="email" name="email_alt" class="form-control @error('email_alt') is-invalid @enderror"
                    placeholder="email.cadangan@email.com" value="{{ old('email_alt', $pegawai->email_alt ?? '') }}">
                @error('email_alt') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3" x-data>
                <label class="form-label">Nomor Rekening</label>
                <input
                    type="text"
                    name="no_rek"
                    class="form-control @error('no_rek') is-invalid @enderror"
                    placeholder="Nomor Rekening Bank"
                    value="{{ old('no_rek', $pegawai->no_rek ?? '') }}"
                    inputmode="numeric"
                    x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')">
                @error('no_rek') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Status</label>
                <input type="text" name="status" class="form-control" value="Aktif" readonly>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">TMT Status <span class="text-danger">*</span></label>
                <input type="date" name="tmt_status" class="form-control @error('tmt_status') is-invalid @enderror"
                    value="{{ old('tmt_status', $pegawai->tmt_status ?? '') }}">
                @error('tmt_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">
            <span class="htmx-indicator spinner-border spinner-border-sm"></span>
            Selesai & Simpan
        </button>
    </div>
</form>

<script>
    document.getElementById('step-label').innerText = "Step 4: Kontak & Finalisasi";
    document.getElementById('form-progress-bar').style.width = "75%";
    document.getElementById('form-progress-bar').classList.remove('progress-bar-animated');

    function handleFinalize(event) {
        // Cek jika request berhasil (status 200)
        if (event.detail.successful) {
            // Set progress bar ke 100%
            const progressBar = document.getElementById('form-progress-bar');
            if (progressBar) {
                progressBar.style.width = "100%";
                progressBar.classList.add('bg-success');
                progressBar.classList.remove('bg-info');
            }

            // Update label
            const stepLabel = document.getElementById('step-label');
            if (stepLabel) {
                stepLabel.innerText = "Selesai!";
            }

            // Close modal setelah animasi selesai (500ms untuk smooth transition)
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('mainModal'));
                if (modal) {
                    modal.hide();
                }
            }, 500);
        }
    }
</script>