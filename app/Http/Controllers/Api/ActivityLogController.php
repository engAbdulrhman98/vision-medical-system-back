<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use App\Http\Resources\ActivityLogResource;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = Activity::with('causer')->latest()->get();
        return ActivityLogResource::collection($logs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
        ]);

        activity()
            ->causedBy(auth()->user())
            ->log($request->input('action'));

        return response()->json([
            'message' => 'Activity logged successfully',
        ], 201);
    }
}
