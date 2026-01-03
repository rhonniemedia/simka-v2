<form hx-put="{{ route('pegawais.finalize', $pegawai->id) }}"
    hx-target="#mainModal-content">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Telepon/WA <span class="text-danger">*</span></label>
                <input type="text" name="telepon" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Email (Opsional)</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Alamat Lengkap</label>
                <textarea name="jalan" class="form-control" rows="2" placeholder="Nama Jalan, No. Rumah..."></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kecamatan</label>
                <input type="text" name="kecamatan" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kabupaten/Kota</label>
                <input type="text" name="kabupaten_kota" class="form-control">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success fw-bold">
            <i class="mdi mdi-content-save-check"></i> Selesaikan & Simpan
        </button>
    </div>
</form>

<script>
    document.getElementById('step-label').innerText = "Step 4: Kontak & Alamat";
    document.getElementById('form-progress-bar').style.width = "100%";
    document.getElementById('form-progress-bar').classList.remove('progress-bar-animated');
</script>