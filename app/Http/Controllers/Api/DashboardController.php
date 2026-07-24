<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Review;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        $products = Product::count();
        $categories = Category::count();
        $brands = Brand::count();
        $pending_reviews = Review::where('is_approved', false)->count();
        $unread_messages = ContactMessage::where('is_read', false)->count();

        return response()->json([
            'total_visits' => 12450,
            'today_visits' => 348,
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'pending_reviews' => $pending_reviews,
            'unread_messages' => $unread_messages
        ]);
    }
}
