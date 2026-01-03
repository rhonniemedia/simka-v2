<form hx-put="{{ route('pegawais.update-step', [$pegawai->id, 3]) }}"
    hx-target="#step-content-placeholder">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="row" x-data="{ pmk: '{{ old('pmk', $pegawai->pmk ?? '') }}' }">
            <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Status Kepegawaian <span class="text-danger">*</span></label>
                <select name="sp_id" class="form-select @error('sp_id') is-invalid @enderror">
                    <option value="" selected disabled>-- Pilih Jenis Status --</option>
                    @foreach($statusPegawais as $sp)
                    <option value="{{ $sp->id }}" {{ old('sp_id', $pegawai->sp_id ?? '') == $sp->id ? 'selected' : '' }}>
                        {{ $sp->nama }}
                    </option>
                    @endforeach
                </select>
                @error('sp_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Nomor Induk Pegawai (NIP)</label>
                <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                    placeholder="18 Digit NIP" value="{{ old('nip', $pegawai->nip ?? '') }}">
                @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Jenis Kepegawaian <span class="text-danger">*</span></label>
                <select name="jp_id" class="form-select @error('jp_id') is-invalid @enderror">
                    <option value="" selected disabled>-- Pilih Jenis Pegawai --</option>
                    @foreach($jenisPegawais as $jp)
                    <option value="{{ $jp->id }}" {{ old('jp_id', $pegawai->jp_id ?? '') == $jp->id ? 'selected' : '' }}>
                        {{ $jp->nama }}
                    </option>
                    @endforeach
                </select>
                @error('jp_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">NUPTK</label>
                <input type="text" name="nuptk" class="form-control @error('nuptk') is-invalid @enderror"
                    placeholder="16 Digit NUPTK" value="{{ old('nuptk', $pegawai->nuptk ?? '') }}">
                @error('nuptk') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                <select name="jab_id" class="form-select @error('jab_id') is-invalid @enderror">
                    <option value="" selected disabled>-- Pilih Jabatan --</option>
                    @foreach($jabatans as $jab)
                    <option value="{{ $jab->id }}" {{ old('jab_id', $pegawai->jab_id ?? '') == $jab->id ? 'selected' : '' }}>
                        {{ $jab->nama }}
                    </option>
                    @endforeach
                </select>
                @error('jab_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Jurusan</label>
                <select name="jurusan_id" class="form-select @error('jurusan_id') is-invalid @enderror">
                    <option value="" selected disabled>-- Pilih Jurusan --</option>
                    @foreach($jurusans as $jur)
                    <option value="{{ $jur->id }}" {{ old('jurusan_id', $pegawai->jurusan_id ?? '') == $jur->id ? 'selected' : '' }}>
                        {{ $jur->nama }}
                    </option>
                    @endforeach
                </select>
                @error('jurusan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <hr class="my-4">

            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">PMK <span class="text-danger">*</span></label>
                <select name="pmk" class="form-select @error('pmk') is-invalid @enderror" x-model="pmk">
                    <option value="" selected disabled>-- Pilih --</option>
                    <option value="ya" {{ old('pmk', $pegawai->pmk ?? '') == 'ya' ? 'selected' : '' }}>Ya</option>
                    <option value="tidak" {{ old('pmk', $pegawai->pmk ?? '') == 'tidak' ? 'selected' : '' }}>Tidak</option>
                </select>
                @error('pmk') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">TMT PMK</label>
                <input type="date" name="pmk_tmt" class="form-control @error('pmk_tmt') is-invalid @enderror"
                    :disabled="pmk !== 'ya'"
                    value="{{ old('pmk_tmt', $pegawai->pmk_tmt ?? '') }}">
                @error('pmk_tmt') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Masa Kerja (Thn)</label>
                <input type="number" name="pmk_thn" class="form-control @error('pmk_thn') is-invalid @enderror"
                    placeholder="Tahun" :disabled="pmk !== 'ya'"
                    value="{{ old('pmk_thn', $pegawai->pmk_thn ?? '') }}">
                @error('pmk_thn') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Masa Kerja (Bln)</label>
                <input type="number" name="pmk_bln" class="form-control @error('pmk_bln') is-invalid @enderror"
                    placeholder="Bulan" :disabled="pmk !== 'ya'"
                    value="{{ old('pmk_bln', $pegawai->pmk_bln ?? '') }}">
                @error('pmk_bln') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
    document.getElementById('step-label').innerText = "Step 2: Kepegawaian";
    document.getElementById('form-progress-bar').style.width = "25%";
</script>