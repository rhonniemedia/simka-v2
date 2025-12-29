<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">{{ $product->exists ? 'Edit' : 'Tambah' }} Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <form hx-post="{{ $product->exists ? route('products.update', $product) : route('products.store') }}"
        hx-target="closest .modal-content"
        hx-swap="outerHTML">

        @csrf
        @if($product->exists) @method('PUT') @endif

        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Nama Produk</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}">
                    @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product->price) }}">
                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">
                <span class="htmx-indicator spinner-border spinner-border-sm"></span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>