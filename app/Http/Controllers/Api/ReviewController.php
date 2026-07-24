<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Http\Resources\ReviewResource;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('product');

        if ($request->has('pending')) {
            $query->where('is_approved', false);
        }

        return ReviewResource::collection($query->latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'reviewer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $review = Review::create([
            'product_id' => $request->product_id,
            'reviewer_name' => $request->reviewer_name,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => false, // always pending by default
        ]);

        activity()
            ->log('تم إضافة تقييم جديد معلق للمنتج ذو الرقم: ' . $request->product_id);

        return response()->json([
            'message' => 'Review submitted successfully and is pending approval',
            'review' => new ReviewResource($review)
        ], 201);
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        activity()
            ->causedBy(auth()->user())
            ->log('تم قبول مراجعة العميل: ' . $review->reviewer_name);

        return response()->json([
            'message' => 'Review approved successfully',
            'review' => new ReviewResource($review)
        ]);
    }

    public function destroy(Review $review)
    {
        $reviewer = $review->reviewer_name;
        $review->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('تم حذف مراجعة العميل: ' . $reviewer);

        return response()->json([
            'message' => 'Review deleted successfully'
        ]);
    }
}
