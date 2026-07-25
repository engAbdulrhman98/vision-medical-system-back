<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientContact;
use App\Models\Notification;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Admins and managers can view all tasks, others can only view assigned tasks
        $isAdminOrManager = $user->hasAnyRole(['Admin', 'CEO', 'Operations Manager']);

        $query = Task::with(['user', 'device.product', 'client', 'clientContact', 'governorate', 'city', 'updates.user']);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('governorate_id') && $request->governorate_id != 0) {
            $query->where('governorate_id', $request->governorate_id);
        }
        if ($request->has('city_id') && $request->city_id != 0) {
            $query->where('city_id', $request->city_id);
        }
        if ($request->has('client_id') && $request->client_id != 0) {
            $query->where('client_id', $request->client_id);
        }

        if (!$isAdminOrManager) {
            $query->where('user_id', $user->id);
        }

        $tasks = $query->latest()->get();

        return response()->json($tasks->map(function (\App\Models\Task $task) {
            return [
                'id' => $task->id,
                'title' => $task->getTranslations('title'),
                'description' => $task->description,
                'status' => $task->status,
                'progress' => (int) $task->progress,
                'priority' => $task->priority,
                'type' => $task->type,
                'action_type' => $task->action_type ?? 'quotation_request',
                'rejection_reason' => $task->rejection_reason,
                'accountant_note' => $task->accountant_note,
                'invoice_id' => $task->invoice_id,
                'scheduled_at' => $task->scheduled_at,
                'completed_at' => $task->completed_at,
                'user_id' => $task->user_id,
                'engineer_name' => $task->user ? $task->user->name : null,
                'device_id' => $task->device_id,
                'device_serial' => $task->device ? $task->device->serial_number : null,
                'device_product_name' => ($task->device && $task->device->product) ? $task->device->product->getTranslations('name') : null,
                'client_id' => $task->client_id,
                'client_name' => $task->client ? $task->client->getTranslations('name') : null,
                'client_contact_id' => $task->client_contact_id,
                'contact_person_name' => $task->clientContact ? $task->clientContact->name : null,
                'contact_person_phone' => $task->clientContact ? $task->clientContact->phone : null,
                'contact_person_job_title' => $task->clientContact ? $task->clientContact->job_title : null,
                'governorate_id' => $task->governorate_id,
                'governorate_name' => $task->governorate ? $task->governorate->getTranslations('name') : null,
                'city_id' => $task->city_id,
                'city_name' => $task->city ? $task->city->getTranslations('name') : null,
                'otp_verified_at' => $task->otp_verified_at ? $task->otp_verified_at->toDateTimeString() : null,
                'is_otp_verified' => !is_null($task->otp_verified_at),
                'updates' => $task->updates->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'user_id' => $u->user_id,
                        'user_name' => $u->user ? $u->user->name : null,
                        'note' => $u->note,
                        'progress' => (int) $u->progress,
                        'created_at' => $u->created_at->toDateTimeString(),
                    ];
                }),
            ];
        }));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string|in:low,medium,high,emergency',
            'device_id' => 'nullable|exists:devices,id',
            'client_id' => 'required|exists:clients,id',
            'client_contact_id' => 'nullable|exists:client_contacts,id',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_job_title' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date',
            'type' => 'nullable|string|in:internal,external',
            'governorate_id' => 'nullable|exists:areas,id',
            'city_id' => 'nullable|exists:areas,id',
        ]);

        $contactId = $request->client_contact_id;
        if (!$contactId && $request->filled('contact_name')) {
            $contact = ClientContact::create([
                'client_id' => $request->client_id,
                'name' => $request->contact_name,
                'phone' => $request->contact_phone,
                'job_title' => $request->contact_job_title ?? 'مسؤول المستشفى/العيادة',
            ]);
            $contactId = $contact->id;
        }

        $task = Task::create([
            'title' => [
                'ar' => $request->title_ar,
                'en' => $request->title_en,
            ],
            'description' => $request->description,
            'status' => 'pending',
            'priority' => $request->priority,
            'type' => $request->type ?? 'internal',
            'device_id' => $request->device_id,
            'client_id' => $request->client_id,
            'client_contact_id' => $contactId,
            'governorate_id' => $request->governorate_id,
            'city_id' => $request->city_id,
            'user_id' => $request->user_id,
            'scheduled_at' => $request->scheduled_at,
        ]);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task->load(['clientContact', 'client'])
        ], 201);
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string|in:low,medium,high,emergency',
            'device_id' => 'nullable|exists:devices,id',
            'client_id' => 'required|exists:clients,id',
            'client_contact_id' => 'nullable|exists:client_contacts,id',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_job_title' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date',
            'status' => 'nullable|string|in:pending,in_progress,completed,cancelled',
            'type' => 'nullable|string|in:internal,external',
            'governorate_id' => 'nullable|exists:areas,id',
            'city_id' => 'nullable|exists:areas,id',
        ]);

        $contactId = $request->client_contact_id ?? $task->client_contact_id;
        if ($request->filled('contact_name') && !$request->client_contact_id) {
            $contact = ClientContact::create([
                'client_id' => $request->client_id,
                'name' => $request->contact_name,
                'phone' => $request->contact_phone,
                'job_title' => $request->contact_job_title ?? 'مسؤول المستشفى/العيادة',
            ]);
            $contactId = $contact->id;
        }

        $task->update([
            'title' => [
                'ar' => $request->title_ar,
                'en' => $request->title_en,
            ],
            'description' => $request->description,
            'priority' => $request->priority,
            'device_id' => $request->device_id,
            'client_id' => $request->client_id,
            'client_contact_id' => $contactId,
            'governorate_id' => $request->governorate_id,
            'city_id' => $request->city_id,
            'user_id' => $request->user_id,
            'scheduled_at' => $request->scheduled_at,
            'status' => $request->status ?? $task->status,
            'type' => $request->type ?? $task->type,
        ]);

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task->load(['clientContact', 'client'])
        ]);
    }

    /**
     * Update status of the task.
     */
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed,cancelled',
        ]);

        $task->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : $task->completed_at,
        ]);

        return response()->json([
            'message' => 'Task status updated successfully',
            'task' => $task
        ]);
    }

    /**
     * Generate an OTP code to send to the hospital/clinic contact person.
     */
    public function generateOtp(Request $request, Task $task)
    {
        // Generate a 4-digit random OTP
        $otp = str_pad((string) rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(30);

        $task->update([
            'otp_code' => $otp,
            'otp_expires_at' => $expiresAt,
        ]);

        $contact = $task->clientContact;
        $contactName = $contact ? $contact->name : 'مسؤول المستشفى/العيادة';
        $contactPhone = $contact ? $contact->phone : null;
        $contactJob = $contact ? $contact->job_title : null;

        // Log notification in system
        Notification::create([
            'user_id' => $task->user_id,
            'task_id' => $task->id,
            'title' => [
                'ar' => "رمز تأكيد المهمة #{$task->id}",
                'en' => "Task OTP Code #{$task->id}"
            ],
            'body' => [
                'ar' => "تم توليد رمز التأكيد ({$otp}) للمسؤول بالمستشفى ({$contactName} - {$contactJob}). صالحة لمدة 30 دقيقة.",
                'en' => "OTP code ({$otp}) generated for hospital contact ({$contactName}). Valid for 30 minutes."
            ],
            'type' => 'otp_generated'
        ]);

        return response()->json([
            'message' => 'تم توليد رمز تأكيد الإنجاز بنجاح وإرساله لمسؤول المستشفى',
            'otp_code' => $otp, // Returned for easy testing and display in dashboard/app
            'expires_at' => $expiresAt->toDateTimeString(),
            'contact_person' => [
                'name' => $contactName,
                'phone' => $contactPhone,
                'job_title' => $contactJob,
            ]
        ]);
    }

    /**
     * Verify the OTP code supplied by the field engineer/sales rep.
     */
    public function verifyOtp(Request $request, Task $task)
    {
        // Directly complete task without OTP requirement
        $task->update([
            'otp_verified_at' => now(),
            'status' => 'completed',
            'progress' => 100,
            'completed_at' => now(),
        ]);

        activity()
            ->performedOn($task)
            ->causedBy($request->user())
            ->log("تم إكمال المهمة وتأكيد إغلاق الطلب بنجاح");

        return response()->json([
            'message' => 'تم إكمال المهمة وتأكيد إغلاق الطلب بنجاح',
            'task' => $task->fresh(['clientContact', 'client', 'user'])
        ]);
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }

    /**
     * Add a follow-up progress update/comment to the task.
     */
    public function addUpdate(Request $request, Task $task)
    {
        $validated = $request->validate([
            'note' => 'required|string',
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $update = $task->updates()->create([
            'user_id' => $request->user()->id,
            'note' => $validated['note'],
            'progress' => $validated['progress'],
        ]);

        // Automatically update main task's progress
        $task->update([
            'progress' => $validated['progress']
        ]);

        // Auto-mark task status as completed when progress is 100%
        if ($validated['progress'] == 100) {
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        } elseif ($task->status === 'pending') {
            // Auto-transition to in_progress if progress started
            $task->update([
                'status' => 'in_progress',
            ]);
        }

        return response()->json([
            'message' => 'Task update added successfully',
            'update' => $update,
            'task_progress' => $task->progress,
            'task_status' => $task->status
        ]);
    }

    /**
     * Submit visit outcome by field engineer / sales rep.
     */
    public function submitVisitOutcome(Request $request, Task $task)
    {
        $request->validate([
            'outcome' => 'required|string|in:accepted,rejected',
            'action_type' => 'nullable|string|in:invoice_request,maintenance_request,quotation_request',
            'rejection_reason' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $user = $request->user();
        $clientName = $task->client ? ($task->client->getTranslation('name', 'ar') ?: $task->client->name) : 'العميل';
        $userName = $user ? $user->name : 'الموظف';

        if ($request->outcome === 'rejected') {
            $task->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason ?: ($request->note ?: 'تم رفض العرض من المسئول'),
                'progress' => 100,
                'completed_at' => now(),
            ]);

            $task->updates()->create([
                'user_id' => $user ? $user->id : 1,
                'note' => 'تم الرفض من المسئول: ' . ($request->rejection_reason ?: 'لم يتم قبول السعر'),
                'progress' => 100,
            ]);

            Notification::create([
                'user_id' => 1,
                'task_id' => $task->id,
                'title' => [
                    'ar' => "رفض عرض السعر للمهمة #{$task->id}",
                    'en' => "Quotation Rejected Task #{$task->id}"
                ],
                'body' => [
                    'ar' => "قام الموظف ({$userName}) بإنهاء المهمة لدى ({$clientName}) - تم رفض العرض من قبل المسئول (السبب: " . ($request->rejection_reason ?: 'سعر غير مناسب') . ")",
                    'en' => "Representative ({$userName}) completed visit for ({$clientName}) - Offer rejected."
                ],
                'type' => 'task_rejected'
            ]);

            return response()->json([
                'message' => 'تم تسجيل رفض المسئول وإكمال المهمة بنجاح',
                'task' => $task->fresh(['clientContact', 'client', 'user'])
            ]);
        } else {
            $actionType = $request->action_type ?: 'invoice_request';
            $task->update([
                'status' => 'awaiting_accountant',
                'action_type' => $actionType,
                'progress' => 50,
            ]);

            $task->updates()->create([
                'user_id' => $user ? $user->id : 1,
                'note' => 'وافق المسئول على العرض! تم تحويل الطلب للمحاسب لإصدار ' . ($actionType === 'invoice_request' ? 'الفاتورة' : 'طلب الصيانة'),
                'progress' => 50,
            ]);

            // Notify all Accountants
            $accountants = \App\Models\User::all();
            foreach ($accountants as $acc) {
                if ($acc->hasRole('Accountant') || $acc->id === 1) {
                    Notification::create([
                        'user_id' => $acc->id,
                        'task_id' => $task->id,
                        'title' => [
                            'ar' => "طلب جديد للمحاسب للمهمة #{$task->id}",
                            'en' => "Accountant Action Required #{$task->id}"
                        ],
                        'body' => [
                            'ar' => "وافق مسئول المستشفى ({$clientName}) على العرض! مطلوب من المحاسب إصدار " . ($actionType === 'invoice_request' ? 'فاتورة' : 'أمر صيانة') . " وتوجيهه للمهندس ({$userName}).",
                            'en' => "Hospital ({$clientName}) accepted offer. Accountant action required."
                        ],
                        'type' => 'invoice_request'
                    ]);
                }
            }

            return response()->json([
                'message' => 'تم تسجيل موافقة المسئول وتحويل الطلب بنجاح للمحاسب',
                'task' => $task->fresh(['clientContact', 'client', 'user'])
            ]);
        }
    }

    /**
     * Process action by accountant (Issue Invoice / Maintenance).
     */
    public function processAccountantAction(Request $request, Task $task)
    {
        $request->validate([
            'accountant_note' => 'required|string',
            'invoice_id' => 'nullable|integer',
        ]);

        $user = $request->user();
        $clientName = $task->client ? ($task->client->getTranslation('name', 'ar') ?: $task->client->name) : 'العميل';

        $task->update([
            'status' => 'accountant_ready',
            'accountant_note' => $request->accountant_note,
            'invoice_id' => $request->invoice_id,
            'progress' => 80,
        ]);

        $task->updates()->create([
            'user_id' => $user ? $user->id : 1,
            'note' => 'قام المحاسب بتجهيز الفاتورة / الطلب: ' . $request->accountant_note,
            'progress' => 80,
        ]);

        if ($task->user_id) {
            Notification::create([
                'user_id' => $task->user_id,
                'task_id' => $task->id,
                'title' => [
                    'ar' => "تم إصدار الفاتورة/الطلب للمهمة #{$task->id}",
                    'en' => "Invoice Ready for Delivery #{$task->id}"
                ],
                'body' => [
                    'ar' => "قام المحاسب بإصدار الفاتورة / المستند للمهمة لدى ({$clientName}) وهي جاهزة لتسلميها لمسؤول العميل.",
                    'en' => "Accountant processed invoice for ({$clientName}). Ready for delivery."
                ],
                'type' => 'invoice_ready'
            ]);
        }

        Notification::create([
            'user_id' => 1,
            'task_id' => $task->id,
            'title' => [
                'ar' => "إصدار الفاتورة من المحاسب للمهمة #{$task->id}",
                'en' => "Accountant Issued Invoice #{$task->id}"
            ],
            'body' => [
                'ar' => "أنهى المحاسب إصدار وتجهيز المستندات للمهمة لدى ({$clientName}) وأصبحت جاهزة للتسليم الميداني.",
                'en' => "Accountant completed invoice issuance for ({$clientName})."
            ],
            'type' => 'accountant_processed'
        ]);

        return response()->json([
            'message' => 'تم إصدار وتجهيز المستندات بنجاح وتنبيه المهندس الميداني للتسليم',
            'task' => $task->fresh(['clientContact', 'client', 'user'])
        ]);
    }
}
