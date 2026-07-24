<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;

class BrandController extends Controller
{
    /**
     * Display a listing of the brands.
     */
    public function index()
    {
        $brands = QueryBuilder::for(Brand::class)
            ->withCount('products')
            ->allowedFilters(
                'name',
                'slug',
                AllowedFilter::callback('search', function (Builder $query, $value) {
                    $query->where(function (Builder $q) use ($value) {
                        $q->where('name->en', 'like', "%{$value}%")
                          ->orWhere('name->ar', 'like', "%{$value}%")
                          ->orWhere('slug', 'like', "%{$value}%");
                    });
                })
            )
            ->allowedSorts('name', 'created_at', 'products_count')
            ->latest();

        $perPage = (int) request('per_page', 15);
        if ($perPage > 100) $perPage = 100;

        $brands = $brands->paginate($perPage)->withQueryString();

        return BrandResource::collection($brands)
            ->response()
            ->header('Cache-Control', 'private, no-cache');
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(StoreBrandRequest $request)
    {
        $validated = $request->validated();

        $brand = Brand::create([
            'name' => [
                'ar' => $validated['name']['ar'],
                'en' => $validated['name']['en'],
            ],
        ]);

        if ($request->hasFile('image_file')) {
            $brand->addMediaFromRequest('image_file')->toMediaCollection('logo');
        } elseif ($request->hasFile('image')) {
            $brand->addMediaFromRequest('image')->toMediaCollection('logo');
        } elseif ($request->filled('image_url')) {
            try {
                $brand->addMediaFromUrl($request->image_url)->toMediaCollection('logo');
            } catch (\Exception $e) {
                // Fail silently or fallback if url is offline/invalid
            }
        }

        $brand->load('media');

        return response()->json([
            'message' => 'Brand created successfully',
            'brand' => new BrandResource($brand),
        ], 201);
    }

    /**
     * Display the specified brand.
     */
    public function show(Brand $brand)
    {
        $brand->load('media');
        return new BrandResource($brand);
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $validated = $request->validated();

        if (isset($validated['name'])) {
            $brand->name = [
                'ar' => $validated['name']['ar'] ?? $brand->getTranslation('name', 'ar'),
                'en' => $validated['name']['en'] ?? $brand->getTranslation('name', 'en'),
            ];
        }

        if ($request->hasFile('image_file')) {
            $brand->clearMediaCollection('logo');
            $brand->addMediaFromRequest('image_file')->toMediaCollection('logo');
        } elseif ($request->hasFile('image')) {
            $brand->clearMediaCollection('logo');
            $brand->addMediaFromRequest('image')->toMediaCollection('logo');
        } elseif ($request->filled('image_url')) {
            try {
                $brand->clearMediaCollection('logo');
                $brand->addMediaFromUrl($request->image_url)->toMediaCollection('logo');
            } catch (\Exception $e) {
                // Fail silently
            }
        }

        $brand->save();
        $brand->load('media');

        return response()->json([
            'message' => 'Brand updated successfully',
            'brand' => new BrandResource($brand),
        ]);
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();

        return response()->json([
            'message' => 'Brand deleted successfully',
        ]);
    }

    /**
     * Export all brands to an Excel file.
     */
    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\BrandsExport, 'brands.xlsx');
    }

    /**
     * Import brands from an uploaded Excel/CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\BrandsImport, $request->file('file'));

        return response()->json([
            'message' => 'Brands imported successfully',
        ]);
    }
}
