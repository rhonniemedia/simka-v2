<div class="table-responsive">
    @if($products->isEmpty())
    <div class="alert alert-info text-center">
        <i class="bi bi-inbox"></i> Tidak ada produk yang ditemukan.
    </div>
    @else
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
            <tr>
                <td><code>{{ $p->sku }}</code></td>
                <td>{{ $p->name }}</td>
                <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-info"
                        hx-get="/products/{{ $p->id }}/edit"
                        hx-target="#mainModal-content"
                        hx-push-url="false"
                        hx-indicator="#loading">
                        Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger"
                        @click="confirmDelete({{ $p->id }}, '{{ addslashes($p->name) }}')">
                        Hapus
                    </button>
                    <button class="btn btn-sm btn-outline-warning"
                        hx-get="/products/{{ $p->id }}"
                        hx-target="#detailModal-content"
                        hx-push-url="false"
                        hx-on::after-swap="Alpine.$data(document.querySelector('[x-data]')).detailModal.show()">
                        Detail
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@if(!$products->isEmpty())
<div class="mt-3">
    {{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
@endif

<script>
    document.addEventListener('alpine:init', () => {
        setTimeout(() => {
            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const page = url.searchParams.get('page');

                    const wrapper = document.querySelector('[x-data]');
                    if (wrapper && window.Alpine) {
                        const app = Alpine.$data(wrapper);
                        if (app) {
                            app.currentPage = page;
                            app.loadProducts(true);
                        }
                    }
                });
            });
        }, 100);
    });
</script>