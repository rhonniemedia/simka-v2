<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Detail Pegawai: {{ $pegawai->nama }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        {{-- Foto & Identitas Utama --}}
        <div class="row mb-4">
            <div class="col-md-3 text-center">
                @if($pegawai->foto)
                <img src="{{ Storage::url($pegawai->foto) }}"
                    alt="{{ $pegawai->nama }}"
                    class="rounded-circle mb-3"
                    style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #e9ecef;">
                @else
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-3 mx-auto"
                    style="width: 120px; height: 120px; font-weight: bold; font-size: 48px; border: 4px solid #e9ecef;">
                    {{ strtoupper(substr($pegawai->nama, 0, 2)) }}
                </div>
                @endif

                @if($pegawai->status === 'aktif')
                <span class="badge badge-success">Aktif</span>
                @elseif($pegawai->status === 'mutasi')
                <span class="badge badge-warning">Mutasi</span>
                @else
                <span class="badge badge-secondary">Pensiun</span>
                @endif
            </div>
            <div class="col-md-9">
                <h4 class="mb-3">{{ $pegawai->nama }}</h4>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <small class="text-muted d-block">NIP</small>
                        <strong>{{ $pegawai->nip ?? '-' }}</strong>
                    </div>
                    <div class="col-md-6 mb-2">
                        <small class="text-muted d-block">NIK</small>
                        <strong>{{ $pegawai->nik }}</strong>
                    </div>
                    <div class="col-md-6 mb-2">
                        <small class="text-muted d-block">Status Pegawai</small>
                        <span class="badge badge-info">{{ $pegawai->statusPegawai->nama ?? '-' }}</span>
                    </div>
                    <div class="col-md-6 mb-2">
                        <small class="text-muted d-block">Jenis Pegawai</small>
                        <strong>{{ $pegawai->jenisPegawai->nama ?? '-' }}</strong>
                    </div>
                    <div class="col-md-6 mb-2">
                        <small class="text-muted d-block">Jabatan</small>
                        <strong>{{ $pegawai->jabatan->nama ?? '-' }}</strong>
                    </div>
                    <div class="col-md-6 mb-2">
                        <small class="text-muted d-block">Jurusan</small>
                        <strong>{{ $pegawai->jurusan->nama ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        {{-- Tabs untuk Detail --}}
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pribadi-tab" data-bs-toggle="tab" data-bs-target="#pribadi" type="button" role="tab">
                    Data Pribadi
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="kepegawaian-tab" data-bs-toggle="tab" data-bs-target="#kepegawaian" type="button" role="tab">
                    Data Kepegawaian
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="kontak-tab" data-bs-toggle="tab" data-bs-target="#kontak" type="button" role="tab">
                    Kontak & Alamat
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="finansial-tab" data-bs-toggle="tab" data-bs-target="#finansial" type="button" role="tab">
                    Data Finansial
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3">
            {{-- Data Pribadi --}}
            <div class="tab-pane fade show active" id="pribadi" role="tabpanel">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">Tempat Lahir</label>
                        <p class="mb-0">{{ $pegawai->t_lahir }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">Tanggal Lahir</label>
                        <p class="mb-0">{{ $pegawai->tgl_lahir ? \Carbon\Carbon::parse($pegawai->tgl_lahir)->format('d F Y') : '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">Jenis Kelamin</label>
                        <p class="mb-0">{{ $pegawai->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">Agama</label>
                        <p class="mb-0">{{ $pegawai->agama }}</p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="text-muted small d-block">Status Kawin & Tanggungan</label>
                        <p class="mb-0">{{ $pegawai->kawin_tanggungan ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Data Kepegawaian --}}
            <div class="tab-pane fade" id="kepegawaian" role="tabpanel">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">NUPTK</label>
                        <p class="mb-0"><code>{{ $pegawai->nuptk ?? '-' }}</code></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">NPWP</label>
                        <p class="mb-0"><code>{{ $pegawai->npwp ?? '-' }}</code></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">Pangkat/Golongan</label>
                        <p class="mb-0">{{ $pegawai->pmk ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">TMT Masa Kerja</label>
                        <p class="mb-0">{{ $pegawai->tmt_mk ? \Carbon\Carbon::parse($pegawai->tmt_mk)->format('d F Y') : '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">TMT Status</label>
                        <p class="mb-0">{{ $pegawai->tmt_status ? \Carbon\Carbon::parse($pegawai->tmt_status)->format('d F Y') : '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Kontak & Alamat --}}
            <div class="tab-pane fade" id="kontak" role="tabpanel">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">Telepon</label>
                        <p class="mb-0">
                            {{ $pegawai->telepon }}
                            <br><small class="text-muted">(Masked: {{ $pegawai->telepon_masked }})</small>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">Email</label>
                        <p class="mb-0">{{ $pegawai->email ?? '-' }}</p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="text-muted small d-block">Alamat Lengkap</label>
                        <p class="mb-0">
                            {{ $pegawai->jalan }}<br>
                            RT {{ $pegawai->rt ?? '-' }} / RW {{ $pegawai->rw ?? '-' }}<br>
                            {{ $pegawai->desa_kelurahan }}, {{ $pegawai->kecamatan }}<br>
                            {{ $pegawai->kabupaten_kota }}, {{ $pegawai->provinsi }}<br>
                            Kode Pos: {{ $pegawai->kode_pos ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Data Finansial --}}
            <div class="tab-pane fade" id="finansial" role="tabpanel">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">Nomor Rekening</label>
                        <p class="mb-0"><code>{{ $pegawai->no_rek ?? '-' }}</code></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">Besaran Gaji</label>
                        <p class="mb-0 text-success fw-bold">
                            @if($pegawai->besaran_gaji)
                            Rp {{ number_format($pegawai->besaran_gaji, 0, ',', '.') }}
                            @else
                            -
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        {{-- Audit Trail --}}
        <div class="row">
            <div class="col-md-6 mb-2">
                <small class="text-muted d-block">Dibuat Oleh</small>
                <strong>{{ $pegawai->creator->name ?? '-' }}</strong>
                <br><small class="text-muted">{{ $pegawai->created_at->format('d F Y, H:i') }}</small>
            </div>
            <div class="col-md-6 mb-2">
                <small class="text-muted d-block">Terakhir Diupdate Oleh</small>
                <strong>{{ $pegawai->updater->name ?? '-' }}</strong>
                <br><small class="text-muted">{{ $pegawai->updated_at->format('d F Y, H:i') }}</small>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button"
            class="btn btn-warning"
            hx-get="/pegawai/{{ $pegawai->id }}/edit"
            hx-target="#mainModal-content"
            hx-push-url="false"
            hx-indicator="#loading"
            data-bs-dismiss="modal">
            Edit Data
        </button>
    </div>
</div>