<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search by name, description, details or slug
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name->ar', 'like', "%{$search}%")
                  ->orWhere('name->en', 'like', "%{$search}%")
                  ->orWhere('description->ar', 'like', "%{$search}%")
                  ->orWhere('description->en', 'like', "%{$search}%")
                  ->orWhere('details->ar', 'like', "%{$search}%")
                  ->orWhere('details->en', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        } elseif ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        } elseif ($request->filled('brand')) {
            $query->whereHas('brand', function($q) use ($request) {
                $q->where('slug', $request->input('brand'));
            });
        }

        // Filter by stock status
        if ($request->filled('in_stock')) {
            $query->where('in_stock', $request->input('in_stock') === '1' || $request->input('in_stock') === 'true');
        }

        // Paginate results
        $products = $query->with(['category', 'brand'])
            ->latest()
            ->paginate($request->input('per_page', 10));

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'sku' => 'nullable|string|max:100',
            'description.ar' => 'required|string',
            'description.en' => 'required|string',
            'details.ar' => 'nullable|string',
            'details.en' => 'nullable|string',
            'image_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm|max:20480',
            'image_url' => 'nullable|string',
            'in_stock' => 'nullable|boolean',
        ]);

        $slug = Str::slug($request->input('name.en'));
        if (empty($slug) || $slug == '-') {
            $slug = str_replace(' ', '-', $request->input('name.en'));
        }

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $imagePath = asset('storage/' . $path);
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        $product = new Product();
        $product->setTranslation('name', 'ar', $request->input('name.ar'));
        $product->setTranslation('name', 'en', $request->input('name.en'));
        $product->setTranslation('description', 'ar', $request->input('description.ar'));
        $product->setTranslation('description', 'en', $request->input('description.en'));
        $product->setTranslation('details', 'ar', $request->input('details.ar') ?? '');
        $product->setTranslation('details', 'en', $request->input('details.en') ?? '');
        $product->category_id = $request->input('category_id');
        $product->brand_id = $request->input('brand_id');
        $product->price = $request->input('price');
        $product->sku = $request->input('sku');
        $product->image = $imagePath;
        $product->in_stock = $request->input('in_stock', true);
        $product->slug = $slug;
        $product->save();

        activity()
            ->causedBy(auth()->user())
            ->log('تم إضافة منتج جديد: ' . $request->input('name.ar'));

        return response()->json([
            'message' => 'Product created successfully',
            'product' => new ProductResource($product)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($idOrSlug)
    {
        $product = Product::with(['category', 'brand', 'approvedReviews'])
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'sku' => 'nullable|string|max:100',
            'description.ar' => 'required|string',
            'description.en' => 'required|string',
            'details.ar' => 'nullable|string',
            'details.en' => 'nullable|string',
            'image_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm|max:20480',
            'image_url' => 'nullable|string',
            'in_stock' => 'nullable|boolean',
        ]);

        $slug = Str::slug($request->input('name.en'));
        if (empty($slug) || $slug == '-') {
            $slug = str_replace(' ', '-', $request->input('name.en'));
        }

        $imagePath = $product->image;
        if ($request->hasFile('image_file')) {
            if ($product->image && str_contains($product->image, asset('storage/'))) {
                $oldPath = Str::after($product->image, asset('storage/'));
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image_file')->store('products', 'public');
            $imagePath = asset('storage/' . $path);
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        $product->setTranslation('name', 'ar', $request->input('name.ar'));
        $product->setTranslation('name', 'en', $request->input('name.en'));
        $product->setTranslation('description', 'ar', $request->input('description.ar'));
        $product->setTranslation('description', 'en', $request->input('description.en'));
        $product->setTranslation('details', 'ar', $request->input('details.ar') ?? '');
        $product->setTranslation('details', 'en', $request->input('details.en') ?? '');
        $product->category_id = $request->input('category_id');
        $product->brand_id = $request->input('brand_id');
        $product->price = $request->input('price');
        $product->sku = $request->input('sku');
        $product->image = $imagePath;
        $product->in_stock = $request->input('in_stock', true);
        $product->slug = $slug;
        $product->save();

        activity()
            ->causedBy(auth()->user())
            ->log('تم تحديث بيانات المنتج: ' . $request->input('name.ar'));

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => new ProductResource($product)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image && str_contains($product->image, asset('storage/'))) {
            $oldPath = Str::after($product->image, asset('storage/'));
            Storage::disk('public')->delete($oldPath);
        }

        $nameAr = $product->getTranslation('name', 'ar');
        $product->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('تم حذف المنتج: ' . $nameAr);

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ProductsExport, 'products.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ProductsImport, $request->file('file'));

        return response()->json([
            'message' => 'Products imported successfully',
        ]);
    }
}
