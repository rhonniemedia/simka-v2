<form hx-put="{{ route('pegawais.update-step', [$pegawai->id, 4]) }}"
    hx-target="#step-content-placeholder">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">NIK (16 Digit) <span class="text-danger">*</span></label>
                <input type="text" name="nik" class="form-control" maxlength="16" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">NIP</label>
                <input type="text" name="nip" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Nomor Rekening</label>
                <input type="text" name="no_rek" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Besaran Gaji</label>
                <input type="number" name="besaran_gaji" class="form-control">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Lanjut ke Alamat <i class="mdi mdi-chevron-right"></i></button>
    </div>
</form>

<script>
    document.getElementById('step-label').innerText = "Step 3: Identitas & Finansial";
    document.getElementById('form-progress-bar').style.width = "50%";
</script>