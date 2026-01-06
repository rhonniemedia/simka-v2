<form hx-post="{{ route('pegawais.store-step1', isset($pegawai) ? $pegawai->id : '') }}"
    hx-target="#step-content-placeholder"
    hx-encoding="multipart/form-data">

    @csrf
    @if(isset($pegawai))
    <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
    @endif

    <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $pegawai->nama ?? '') }}" placeholder="Nama Lengkap">
                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="jk" class="form-select @error('jk') is-invalid @enderror">
                    <option value="" selected disabled>-- Pilih Jenis Kelamin--</option>
                    <option value="L" {{ old('jk', $pegawai->jk ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jk', $pegawai->jk ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jk') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Agama <span class="text-danger">*</span></label>
                <select name="agama" class="form-select @error('agama') is-invalid @enderror">
                    <option value="" selected disabled>-- Pilih Agama --</option>
                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu', 'Lainnya'] as $agm)
                    <option value="{{ $agm }}" {{ old('agama', $pegawai->agama ?? '') == $agm ? 'selected' : '' }}>
                        {{ $agm }}
                    </option>
                    @endforeach
                </select>
                @error('agama') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                <input type="text" name="t_lahir" class="form-control @error('t_lahir') is-invalid @enderror" value="{{ old('t_lahir', $pegawai->t_lahir ?? '') }}" placeholder="Tempat Kelahiran">
                @error('t_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="date" name="tgl_lahir" class="form-control @error('tgl_lahir') is-invalid @enderror" value="{{ old('tgl_lahir', $pegawai->tgl_lahir ?? '') }}">
                @error('tgl_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Nomor Induk Kependudukan (NIK) <span class="text-danger">*</span></label>
                <input type="number" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $pegawai->nik ?? '') }}" placeholder="16 Digit Nomor Induk Kependudukan (NIK)">
                @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Status Perkawinan <span class="text-danger">*</span></label>
                <select name="kawin_tanggungan" class="form-select @error('kawin_tanggungan') is-invalid @enderror">
                    <option value="" selected disabled>-- Pilih Status --</option>
                    <option value="TK/0" {{ old('kawin_tanggungan', $pegawai->kawin_tanggungan ?? '') == 'TK/0' ? 'selected' : '' }}>TK/0 (Tidak Kawin)</option>
                    <option value="K/0" {{ old('kawin_tanggungan', $pegawai->kawin_tanggungan ?? '') == 'K/0' ? 'selected' : '' }}>K/0 (Kawin, 0 Tanggungan)</option>
                    <option value="K/1" {{ old('kawin_tanggungan', $pegawai->kawin_tanggungan ?? '') == 'K/1' ? 'selected' : '' }}>K/1 (Kawin, 1 Tanggungan)</option>
                    <option value="K/2" {{ old('kawin_tanggungan', $pegawai->kawin_tanggungan ?? '') == 'K/2' ? 'selected' : '' }}>K/2 (Kawin, 2 Tanggungan)</option>
                    <option value="K/3" {{ old('kawin_tanggungan', $pegawai->kawin_tanggungan ?? '') == 'K/3' ? 'selected' : '' }}>K/3 (Kawin, 3+ Tanggungan)</option>
                    <option value="Janda/Duda" {{ old('kawin_tanggungan', $pegawai->kawin_tanggungan ?? '') == 'Janda/Duda' ? 'selected' : '' }}>Janda/Duda</option>
                </select>
                @error('kawin_tanggungan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Nomor Pokok Wajib Pajak (NPWP) <span class="text-danger">*</span></label>
                <input type="number" name="npwp" class="form-control @error('npwp') is-invalid @enderror" value="{{ old('npwp', $pegawai->npwp ?? '') }}" placeholder="Nomor Pokok Wajib Pajak (NPWP)">
                @error('npwp') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Foto Profil</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
                @if(isset($pegawai) && $pegawai->foto)
                <small class="text-muted d-block mt-1">
                    <i class="mdi mdi-check-circle text-success"></i> Foto sudah terupload
                </small>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">
            <span class="htmx-indicator spinner-border spinner-border-sm"></span>
            Simpan dan Lanjut
        </button>
    </div>
</form>

<script>
    document.getElementById('step-label').innerText = "{{ isset($pegawai) && $pegawai->exists ? $pegawai->nama : 'Step 1: Data Pribadi' }}";
    document.getElementById('form-progress-bar').style.width = "0%"; // Belum simpan = 0%
</script>