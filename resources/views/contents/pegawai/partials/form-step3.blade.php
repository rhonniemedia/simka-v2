<form hx-put="{{ route('pegawais.update-step', [isset($pegawai) ? $pegawai->id : '', 4]) }}"
    hx-target="#step-content-placeholder">
    @method('PUT')
    @csrf

    <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
        <div class="row">
            @if(isset($pegawai))
            <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
            @endif

            <div class="col-md-12 mb-3">
                <label class="form-label">Jalan</label>
                <input type="text" name="jalan" class="form-control @error('jalan') is-invalid @enderror"
                    placeholder="Nama Jalan atau Dusun" value="{{ old('jalan', $pegawai->jalan ?? '') }}">
                @error('jalan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">RT</label>
                <input type="text" name="rt" class="form-control @error('rt') is-invalid @enderror"
                    placeholder="000" maxlength="3" value="{{ old('rt', $pegawai->rt ?? '') }}">
                @error('rt') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">RW</label>
                <input type="text" name="rw" class="form-control @error('rw') is-invalid @enderror"
                    placeholder="000" maxlength="3" value="{{ old('rw', $pegawai->rw ?? '') }}">
                @error('rw') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Desa / Kelurahan</label>
                <input type="text" name="desa_kelurahan" class="form-control @error('desa_kelurahan') is-invalid @enderror"
                    placeholder="Nama Desa atau Kelurahan" value="{{ old('desa_kelurahan', $pegawai->desa_kelurahan ?? '') }}">
                @error('desa_kelurahan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Kecamatan</label>
                <input type="text" name="kecamatan" class="form-control @error('kecamatan') is-invalid @enderror"
                    placeholder="Nama Kecamatan" value="{{ old('kecamatan', $pegawai->kecamatan ?? '') }}">
                @error('kecamatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Kabupaten / Kota</label>
                <input type="text" name="kabupaten_kota" class="form-control @error('kabupaten_kota') is-invalid @enderror"
                    placeholder="Nama Kabupaten atau Kota" value="{{ old('kabupaten_kota', $pegawai->kabupaten_kota ?? '') }}">
                @error('kabupaten_kota') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Provinsi</label>
                <input type="text" name="provinsi" class="form-control @error('provinsi') is-invalid @enderror"
                    placeholder="Nama Provinsi" value="{{ old('provinsi', $pegawai->provinsi ?? '') }}">
                @error('provinsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Kode Pos</label>
                <input type="text" name="kode_pos" class="form-control @error('kode_pos') is-invalid @enderror"
                    placeholder="5 Digit" maxlength="5" value="{{ old('kode_pos', $pegawai->kode_pos ?? '') }}">
                @error('kode_pos') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">
            <span class="htmx-indicator spinner-border spinner-border-sm"></span>
            Simpan dan Lanjut
        </button>
    </div>
</form>

<script>
    document.getElementById('step-label').innerText = "{{ isset($pegawai) ? $pegawai->nama : 'Step 3: Alamat' }}";
    document.getElementById('form-progress-bar').style.width = "50%";
</script>