<div class="modal-header">
    <h5 class="modal-title text-dark">
        <i class="mdi mdi-clock-outline me-2"></i>Daftar Pengisian Tertunda
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-0">
    <div class="list-group list-group-flush">
        @forelse($drafts as $draft)
        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
            <div>
                <h6 class="mb-1 fw-bold">{{ $draft->nama }}</h6>
                <p class="mb-0 small text-muted">
                    <i class="mdi mdi-calendar-edit"></i> Dibuat: {{ $draft->created_at->diffForHumans() }}
                </p>
            </div>
            <button class="btn btn-primary"
                hx-get="{{ route('pegawais.resume', $draft->id) }}"
                hx-target="#mainModal-content"
                hx-indicator="#loading">
                Lanjutkan <i class="mdi mdi-arrow-right"></i>
            </button>
        </div>
        @empty
        <div class="p-5 text-center">
            <i class="mdi mdi-clipboard-check-outline mdi-48px text-success"></i>
            <p class="mt-2 text-muted">Tidak ada pengisian yang tertunda.</p>
        </div>
        @endforelse
    </div>
</div>
<div class="modal-footer bg-light">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>