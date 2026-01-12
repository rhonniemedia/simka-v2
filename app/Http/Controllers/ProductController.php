<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\HtmxResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    use HtmxResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::query()
            // Filter pencarian berdasarkan nama atau SKU jika ada
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            // Filter harga minimal
            ->when($request->filled('min_price'), function ($query) use ($request) {
                $query->where('price', '>=', $request->min_price);
            })
            // Filter harga maksimal
            ->when($request->filled('max_price'), function ($query) use ($request) {
                $query->where('price', '<=', $request->max_price);
            })
            ->latest()
            // Mengambil jumlah per halaman, otomatis default ke 10 jika tidak sesuai
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        // Mengembalikan partial table jika request berasal dari HTMX
        if ($request->header('HX-Request')) {
            return view('contents.product.partials.table', compact('products'));
        }

        return view('contents.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('contents.product.partials.form', ['product' => new Product()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'sku' => 'required|string|max:255|unique:products'
            ]);

            Product::create($validated);

            return $this->successResponse('productSaved', 'Produk berhasil ditambahkan.');
        } catch (ValidationException $e) {
            // Beritahu trait untuk menggunakan view 'partials.form' dengan alias variabel 'product'
            return $this->validationErrorResponse(new Product(), $e, 'contents.product.partials.form', 'product');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('contents.product.partials.detail', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('contents.product.partials.form', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'sku' => ['required', 'string', 'max:255', Rule::unique('products')->ignore($product->id)]
            ]);

            $product->fill($validated);

            if (!$product->isDirty()) {
                // Kita harus kirim event secara eksplisit sekarang agar global
                return $this->infoResponse('Tidak Ada Perubahan', 'Data tetap sama.', 'productUpdated');
            }

            $product->save();

            return $this->successResponse('productUpdated', 'Produk berhasil diperbarui.');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($product, $e, 'contents.product.partials.form', 'product');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return $this->successResponse('productUpdated', 'Produk berhasil dihapus.');
    }
}
