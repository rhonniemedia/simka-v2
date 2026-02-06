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
                    <p class="mb-0"> Aksi </p>
                    <small>Edit | Delete</small>
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
                            hx-push-url="false">
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
                        <p class="mb-0"> {{ $pegawai->telepon }}</p>
                        <small>{{ $pegawai->email }}</small>
                    </div>
                </td>
                <td>
                    <button type="button"
                        class="btn btn-sm btn-outline-info btn-icon-only"
                        title="Edit"
                        hx-get="/pegawais/{{ $pegawai->id }}/edit"
                        hx-target="#mainModal-content"
                        hx-push-url="false">
                        <i class="mdi mdi-pencil btn-icon"></i>
                        <span class="spinner-border spinner-border-sm htmx-indicator-custom"></span>
                    </button>


                    <button class="btn btn-sm btn-outline-danger"
                        title="Hapus"
                        @click="confirmDelete('{{ $pegawai->id }}', '{{ addslashes($pegawai->nama) }}')">
                        <i class="mdi mdi-delete"></i>
                    </button>
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