<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(
            Category::withCount('products')->latest()->get()
        )->response()->header('Cache-Control', 'public, max-age=60');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name.ar' => 'required|string',
            'name.en' => 'required|string',
            'description.ar' => 'nullable|string',
            'description.en' => 'nullable|string',
            'image_file' => 'nullable|image|max:2048',
            'image_url' => 'nullable|string',
        ]);

        $slug = Str::slug($request->input('name.en'));
        if (empty($slug) || $slug == '-') {
            $slug = str_replace(' ', '-', $request->input('name.en'));
        }

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $imagePath = asset('storage/' . $path);
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        $category = new Category();
        $category->setTranslation('name', 'ar', $request->input('name.ar'));
        $category->setTranslation('name', 'en', $request->input('name.en'));
        $category->setTranslation('description', 'ar', $request->input('description.ar') ?? '');
        $category->setTranslation('description', 'en', $request->input('description.en') ?? '');
        $category->slug = $slug;
        $category->image = $imagePath;
        $category->save();

        activity()
            ->causedBy(auth()->user())
            ->log('تم إضافة قسم جديد: ' . $request->input('name.ar'));

        return response()->json([
            'message' => 'Category created successfully',
            'category' => new CategoryResource($category)
        ], 201);
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name.ar' => 'required|string',
            'name.en' => 'required|string',
            'description.ar' => 'nullable|string',
            'description.en' => 'nullable|string',
            'image_file' => 'nullable|image|max:2048',
            'image_url' => 'nullable|string',
        ]);

        $slug = Str::slug($request->input('name.en'));
        if (empty($slug) || $slug == '-') {
            $slug = str_replace(' ', '-', $request->input('name.en'));
        }

        $imagePath = $category->image;
        if ($request->hasFile('image_file')) {
            if ($category->image && str_contains($category->image, asset('storage/'))) {
                $oldPath = Str::after($category->image, asset('storage/'));
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image_file')->store('categories', 'public');
            $imagePath = asset('storage/' . $path);
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        $category->setTranslation('name', 'ar', $request->input('name.ar'));
        $category->setTranslation('name', 'en', $request->input('name.en'));
        $category->setTranslation('description', 'ar', $request->input('description.ar') ?? '');
        $category->setTranslation('description', 'en', $request->input('description.en') ?? '');
        $category->slug = $slug;
        $category->image = $imagePath;
        $category->save();

        activity()
            ->causedBy(auth()->user())
            ->log('تم تحديث القسم: ' . $request->input('name.ar'));

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => new CategoryResource($category)
        ]);
    }

    public function destroy(Category $category)
    {
        if ($category->image && str_contains($category->image, asset('storage/'))) {
            $oldPath = Str::after($category->image, asset('storage/'));
            Storage::disk('public')->delete($oldPath);
        }

        $name = $category->getTranslation('name', 'ar');
        $category->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('تم حذف القسم: ' . $name);

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CategoriesExport, 'categories.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\CategoriesImport, $request->file('file'));

        return response()->json([
            'message' => 'Categories imported successfully',
        ]);
    }
}
