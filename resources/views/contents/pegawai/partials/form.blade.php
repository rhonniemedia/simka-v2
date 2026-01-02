<div class="modal-content">
    <form hx-post="{{ $pegawai->exists ? route('pegawais.update', $pegawai->id) : route('pegawais.store') }}"
        hx-encoding="multipart/form-data"
        hx-indicator="#loading">

        @if($pegawai->exists)
        @method('PUT')
        @endif

        <div class="modal-header">
            <h5 class="modal-title">{{ $pegawai->exists ? 'Edit Pegawai' : 'Tambah Pegawai' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

            <!-- Data Pribadi -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong>👤 Data Pribadi</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Nama -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama', $pegawai->nama) }}" required>
                            @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jk" class="form-select @error('jk') is-invalid @enderror" required>
                                <option value="">Pilih...</option>
                                <option value="L" {{ old('jk', $pegawai->jk) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jk', $pegawai->jk) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Agama -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Agama <span class="text-danger">*</span></label>
                            <select name="agama" class="form-select @error('agama') is-invalid @enderror" required>
                                <option value="">Pilih...</option>
                                <option value="Islam" {{ old('agama', $pegawai->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama', $pegawai->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama', $pegawai->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama', $pegawai->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Buddha" {{ old('agama', $pegawai->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                <option value="Konghucu" {{ old('agama', $pegawai->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                            </select>
                            @error('agama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tempat Lahir -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" name="t_lahir" class="form-control @error('t_lahir') is-invalid @enderror"
                                value="{{ old('t_lahir', $pegawai->t_lahir) }}" required>
                            @error('t_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_lahir" class="form-control @error('tgl_lahir') is-invalid @enderror"
                                value="{{ old('tgl_lahir', $pegawai->tgl_lahir) }}" required>
                            @error('tgl_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kawin & Tanggungan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status Kawin & Tanggungan</label>
                            <input type="text" name="kawin_tanggungan" class="form-control @error('kawin_tanggungan') is-invalid @enderror"
                                value="{{ old('kawin_tanggungan', $pegawai->kawin_tanggungan) }}" placeholder="Contoh: K/2">
                            @error('kawin_tanggungan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Foto -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Foto</label>
                            <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                            @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($pegawai->foto)
                            <small class="text-muted">Foto saat ini: {{ basename($pegawai->foto) }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Kepegawaian -->
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <strong>💼 Data Kepegawaian</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Status Pegawai -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status Pegawai <span class="text-danger">*</span></label>
                            <select name="sp_id" class="form-select @error('sp_id') is-invalid @enderror" required>
                                <option value="">Pilih...</option>
                                @foreach($statusPegawais as $sp)
                                <option value="{{ $sp->id }}" {{ old('sp_id', $pegawai->sp_id) == $sp->id ? 'selected' : '' }}>
                                    {{ $sp->nama }}
                                </option>
                                @endforeach
                            </select>
                            @error('sp_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Jenis Pegawai -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Pegawai <span class="text-danger">*</span></label>
                            <select name="jp_id" class="form-select @error('jp_id') is-invalid @enderror" required>
                                <option value="">Pilih...</option>
                                @foreach($jenisPegawais as $jp)
                                <option value="{{ $jp->id }}" {{ old('jp_id', $pegawai->jp_id) == $jp->id ? 'selected' : '' }}>
                                    {{ $jp->nama }}
                                </option>
                                @endforeach
                            </select>
                            @error('jp_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Jabatan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <select name="jab_id" class="form-select @error('jab_id') is-invalid @enderror" required>
                                <option value="">Pilih...</option>
                                @foreach($jabatans as $jab)
                                <option value="{{ $jab->id }}" {{ old('jab_id', $pegawai->jab_id) == $jab->id ? 'selected' : '' }}>
                                    {{ $jab->nama }}
                                </option>
                                @endforeach
                            </select>
                            @error('jab_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Jurusan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                            <select name="jurusan_id" class="form-select @error('jurusan_id') is-invalid @enderror" required>
                                <option value="">Pilih...</option>
                                @foreach($jurusans as $jur)
                                <option value="{{ $jur->id }}" {{ old('jurusan_id', $pegawai->jurusan_id) == $jur->id ? 'selected' : '' }}>
                                    {{ $jur->nama }}
                                </option>
                                @endforeach
                            </select>
                            @error('jurusan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Aktif -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status Aktif <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">Pilih...</option>
                                <option value="aktif" {{ old('status', $pegawai->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="mutasi" {{ old('status', $pegawai->status) == 'mutasi' ? 'selected' : '' }}>Mutasi</option>
                                <option value="pensiun" {{ old('status', $pegawai->status) == 'pensiun' ? 'selected' : '' }}>Pensiun</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- TMT Status -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">TMT Status</label>
                            <input type="date" name="tmt_status" class="form-control @error('tmt_status') is-invalid @enderror"
                                value="{{ old('tmt_status', $pegawai->tmt_status) }}">
                            @error('tmt_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- PMK -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">PMK</label>
                            <input type="text" name="pmk" class="form-control @error('pmk') is-invalid @enderror"
                                value="{{ old('pmk', $pegawai->pmk) }}">
                            @error('pmk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- TMT MK -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">TMT MK</label>
                            <input type="date" name="tmt_mk" class="form-control @error('tmt_mk') is-invalid @enderror"
                                value="{{ old('tmt_mk', $pegawai->tmt_mk) }}">
                            @error('tmt_mk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nomor Identitas -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <strong>🆔 Nomor Identitas</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- NIP -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                                value="{{ old('nip', $pegawai->nip) }}">
                            @error('nip')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- NIK -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIK <span class="text-danger">*</span></label>
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                                value="{{ old('nik', $pegawai->nik) }}" required maxlength="16">
                            @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- NUPTK -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NUPTK</label>
                            <input type="text" name="nuptk" class="form-control @error('nuptk') is-invalid @enderror"
                                value="{{ old('nuptk', $pegawai->nuptk) }}">
                            @error('nuptk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- NPWP -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NPWP</label>
                            <input type="text" name="npwp" class="form-control @error('npwp') is-invalid @enderror"
                                value="{{ old('npwp', $pegawai->npwp) }}">
                            @error('npwp')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Finansial -->
            <div class="card mb-3">
                <div class="card-header bg-warning text-dark">
                    <strong>💰 Data Finansial</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- No Rekening -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Rekening</label>
                            <input type="text" name="no_rek" class="form-control @error('no_rek') is-invalid @enderror"
                                value="{{ old('no_rek', $pegawai->no_rek) }}">
                            @error('no_rek')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Besaran Gaji -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Besaran Gaji</label>
                            <input type="number" name="besaran_gaji" class="form-control @error('besaran_gaji') is-invalid @enderror"
                                value="{{ old('besaran_gaji', $pegawai->besaran_gaji) }}" min="0" step="1000">
                            @error('besaran_gaji')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kontak -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong>📞 Kontak</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Telepon -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror"
                                value="{{ old('telepon', $pegawai->telepon) }}" required>
                            @error('telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $pegawai->email) }}">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alamat -->
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">
                    <strong>🏠 Alamat</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Jalan -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Jalan <span class="text-danger">*</span></label>
                            <input type="text" name="jalan" class="form-control @error('jalan') is-invalid @enderror"
                                value="{{ old('jalan', $pegawai->jalan) }}" required>
                            @error('jalan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- RT -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">RT</label>
                            <input type="text" name="rt" class="form-control @error('rt') is-invalid @enderror"
                                value="{{ old('rt', $pegawai->rt) }}" maxlength="5">
                            @error('rt')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- RW -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">RW</label>
                            <input type="text" name="rw" class="form-control @error('rw') is-invalid @enderror"
                                value="{{ old('rw', $pegawai->rw) }}" maxlength="5">
                            @error('rw')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Desa/Kelurahan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Desa/Kelurahan <span class="text-danger">*</span></label>
                            <input type="text" name="desa_kelurahan" class="form-control @error('desa_kelurahan') is-invalid @enderror"
                                value="{{ old('desa_kelurahan', $pegawai->desa_kelurahan) }}" required>
                            @error('desa_kelurahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kecamatan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                            <input type="text" name="kecamatan" class="form-control @error('kecamatan') is-invalid @enderror"
                                value="{{ old('kecamatan', $pegawai->kecamatan) }}" required>
                            @error('kecamatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kabupaten/Kota -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                            <input type="text" name="kabupaten_kota" class="form-control @error('kabupaten_kota') is-invalid @enderror"
                                value="{{ old('kabupaten_kota', $pegawai->kabupaten_kota) }}" required>
                            @error('kabupaten_kota')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Provinsi -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                            <input type="text" name="provinsi" class="form-control @error('provinsi') is-invalid @enderror"
                                value="{{ old('provinsi', $pegawai->provinsi) }}" required>
                            @error('provinsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kode Pos -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" name="kode_pos" class="form-control @error('kode_pos') is-invalid @enderror"
                                value="{{ old('kode_pos', $pegawai->kode_pos) }}" maxlength="10">
                            @error('kode_pos')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">
                <i class="mdi mdi-content-save"></i> Simpan
            </button>
        </div>
    </form>
</div>