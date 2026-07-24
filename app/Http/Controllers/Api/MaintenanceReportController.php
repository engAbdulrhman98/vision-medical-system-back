<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceReport;
use Illuminate\Http\Request;

class MaintenanceReportController extends Controller
{
    /**
     * Display a listing of the maintenance reports.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdminOrManager = $user->hasAnyRole(['Admin', 'CEO', 'Operations Manager']);

        $query = MaintenanceReport::with(['task.device.product', 'task.client']);

        if (!$isAdminOrManager) {
            // Technicians can only see reports of tasks assigned to them
            $query->whereHas('task', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $reports = $query->latest()->get();

        return response()->json($reports->map(function (\App\Models\MaintenanceReport $report) {
            return [
                'id' => $report->id,
                'task_id' => $report->task_id,
                'summary' => $report->getTranslations('summary'),
                'findings' => $report->findings,
                'actions_taken' => $report->actions_taken,
                'status' => $report->status,
                'created_at' => $report->created_at->toDateTimeString(),
                'deviceName' => ($report->task && $report->task->device && $report->task->device->product) 
                    ? $report->task->device->product->getTranslations('name') 
                    : null,
            ];
        }));
    }

    /**
     * Store a newly created maintenance report.
     */
    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'summary_ar' => 'required|string|max:255',
            'summary_en' => 'required|string|max:255',
            'findings' => 'required|string',
            'actions_taken' => 'required|string',
            'status' => 'nullable|string',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('maintenance_reports', 'public');
        }

        $report = MaintenanceReport::create([
            'task_id' => $request->task_id,
            'summary' => [
                'ar' => $request->summary_ar,
                'en' => $request->summary_en,
            ],
            'findings' => $request->findings,
            'actions_taken' => $request->actions_taken,
            'status' => $request->status ?? 'submitted',
            'attachment' => $attachmentPath,
        ]);

        return response()->json([
            'message' => 'Maintenance report created successfully',
            'data' => [
                'id' => $report->id,
                'task_id' => $report->task_id,
                'summary' => $report->getTranslations('summary'),
                'findings' => $report->findings,
                'actions_taken' => $report->actions_taken,
                'status' => $report->status,
                'attachment' => $report->attachment ? asset('storage/' . $report->attachment) : null,
                'created_at' => $report->created_at->toDateTimeString(),
            ]
        ], 201);
    }
}
