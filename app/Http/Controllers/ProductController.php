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
        $query = Product::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Per page
        $perPage = $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $products = $query->latest()->paginate($perPage)->withQueryString();

        if ($request->header('HX-Request')) {
            return view('partials.table', compact('products'));
        }

        return view('contents.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('partials.form', ['product' => new Product()]);
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
            return $this->validationErrorResponse(new Product(), $e, 'partials.form', 'product');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('partials.form', compact('product'));
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
            return $this->validationErrorResponse($product, $e, 'partials.form', 'product');
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
