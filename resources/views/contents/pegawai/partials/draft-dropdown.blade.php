@forelse($drafts as $draft)
<li>
    <a class="dropdown-item"
        href="#"
        hx-get="{{ route('pegawais.resume', $draft->id) }}"
        hx-target="#mainModal .modal-content"
        hx-swap="innerHTML"
        hx-push-url="false"
        hx-on:htmx:after-request="bootstrap.Modal.getOrCreateInstance(document.getElementById('mainModal')).show()">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="fw-bold text-dark">{{ $draft->nama }}</div>
                <small class="text-muted">
                    <i class="mdi mdi-clock-outline"></i> {{ $draft->created_at->diffForHumans() }}
                </small>
            </div>
            <button class="btn btn-sm btn-outline-secondary"
                type="button"
                title="Lanjutkan">
                <i class="mdi mdi-arrow-right text-secondary"></i>
            </button>
        </div>
    </a>
</li>
@empty
<li class="text-center p-3">
    <i class="mdi mdi-clipboard-check-outline mdi-36px text-success"></i>
    <p class="mb-0 mt-2 text-muted small">Tidak ada pengisian yang tertunda</p>
</li>
@endforelse