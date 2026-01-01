<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Detail Produk: {{ $product->name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-12 mb-3">
                <label class="text-muted small d-block">Nama Produk</label>
                <p class="h5">{{ $product->name }}</p>
            </div>

            <div class="col-md-6 mb-3">
                <label class="text-muted small d-block">SKU</label>
                <p class="fw-bold">{{ $product->sku ?? '-' }}</p>
            </div>

            <div class="col-md-6 mb-3">
                <label class="text-muted small d-block">Harga</label>
                <p class="text-success fw-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            </div>

            <div class="col-12 mb-3">
                <label class="text-muted small d-block">Deskripsi</label>
                <p class="text-secondary">{{ $product->description ?? 'Tidak ada deskripsi produk.' }}</p>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
    </div>
</div>