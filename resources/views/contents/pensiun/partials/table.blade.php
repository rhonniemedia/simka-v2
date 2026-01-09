<div class="table-responsive">
    @if($pegawais->isEmpty())
    <div class="alert alert-info text-center">
        <i class="mdi mdi-alert-circle-outline"></i> Tidak ada data pegawai yang ditemukan.
    </div>
    @else
    <table class="table table-hover align-middle">
        <thead class="bg-light">
            <tr>
                <th style="width: 30%;">
                    <p class="mb-0"> Pegawai </p>
                    <small>Nama | Jabatan</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0"> Kepegawaian </p>
                    <small>Nomor Induk Pegawai | Status</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0"> Kontak </p>
                    <small>Telepon | Email</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0"> Mutasi </p>
                    <small>Status | TMT</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawais as $pegawai)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <a href="javascript:void(0)"
                            class="link-reset"
                            hx-get="/pegawais/{{ $pegawai->id }}"
                            hx-target="#detailModal-content"
                            hx-push-url="false"
                            hx-on::after-swap="Alpine.$data(document.querySelector('[x-data]')).detailModal.show()">
                            <div class="d-flex align-items-center">
                                @if($pegawai->foto)
                                <img src="{{ Storage::url($pegawai->foto) }}"
                                    alt="{{ $pegawai->nama }}"
                                    class="rounded-circle"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                @php
                                $bgClass = $pegawai->jk === 'L' ? 'bg-warning' : 'bg-success';
                                @endphp

                                <div class="rounded-circle {{ $bgClass }} text-white d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px; font-weight: bold; font-size: 14px;">
                                    {{ strtoupper(substr($pegawai->nama, 0, 2)) }}
                                </div>
                                @endif
                                <div class="table-user-name ml-3">
                                    <p class="mb-0 font-weight-medium">{{ $pegawai->nama }}</p>
                                    <small>{{ $pegawai->jabatan->nama ?? '-' }}</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </td>
                <td>
                    <div>
                        @if ($pegawai->nip != '')
                        <p class="mb-0">{{ $pegawai->nip }}</p>
                        @else
                        <b>{{ $pegawai->statusPegawai->nama }}</b>
                        @endif

                        @if ($pegawai->nip != '')
                        <small>Status. <b>{{ $pegawai->statusPegawai->nama }}</b></small>
                        @endif
                    </div>
                </td>
                <td>
                    <div>
                        <p class="mb-0 font-weight-medium"> {{ $pegawai->telepon }}</p>
                        <small>{{ $pegawai->email }}</small>
                    </div>
                </td>
                <td>
                    @php
                    // Mapping warna berdasarkan status enum
                    $statusColor = [
                    'pindah' => 'bg-info',
                    'mundur' => 'bg-warning',
                    'pensiun' => 'bg-success',
                    'meninggal' => 'bg-danger',
                    ];
                    @endphp

                    <span class="badge {{ $statusColor[$pegawai->status] ?? 'bg-secondary' }}">
                        {{ $pegawai->status_label }}
                    </span>
                    <p class="mb-0"><small>{{ $pegawai->tmt_status ? $pegawai->tmt_status->translatedFormat('d F Y') : '-' }}</small></p>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@if(!$pegawais->isEmpty())
<div class="mt-3">
    {{ $pegawais->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
@endif