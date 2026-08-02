<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $tasks = Task::all();

        if ($users->isEmpty()) {
            return;
        }

        $notificationTemplates = [
            [
                'title' => ['ar' => 'تم تعيين مهمة جديدة لك', 'en' => 'New Task Assigned'],
                'body' => ['ar' => 'تم تكليفك بمهمة فحص وصيانة جهاز في موقع العميل.', 'en' => 'You have been assigned to a maintenance task.'],
                'type' => 'task_assigned',
            ],
            [
                'title' => ['ar' => 'تأكيد إغلاق المهمة', 'en' => 'Task Completion Verified'],
                'body' => ['ar' => 'تم إكمال المهمة بنجاح وإغلاق الطلب.', 'en' => 'Task completed successfully.'],
                'type' => 'task_completed',
            ],
            [
                'title' => ['ar' => 'طلب عرض سعر جديد للمبيعات', 'en' => 'New Quotation Request'],
                'body' => ['ar' => 'تم إنشاء طلب عرض سعر جديد بحاجة لمراجعة الاعتماد.', 'en' => 'A new quotation request requires review.'],
                'type' => 'quotation_created',
            ],
            [
                'title' => ['ar' => 'تقديم تقرير صيانة جديد', 'en' => 'Maintenance Report Submitted'],
                'body' => ['ar' => 'قام مهندس الموقع برفع تقرير الصيانة التشخيصي.', 'en' => 'Engineer submitted a diagnostic maintenance report.'],
                'type' => 'report_submitted',
            ],
            [
                'title' => ['ar' => 'طلب إصدار فاتورة وتحصيل', 'en' => 'Invoice Request Pending'],
                'body' => ['ar' => 'تم رفع طلب فاتورة جديدة بقيمة إجمالية للتحصيل.', 'en' => 'New invoice request is pending collection.'],
                'type' => 'invoice_request',
            ],
        ];

        for ($i = 1; $i <= 20; $i++) {
            $user = $users[($i - 1) % $users->count()];
            $task = $tasks->isNotEmpty() ? $tasks[($i - 1) % $tasks->count()] : null;
            $tmpl = $notificationTemplates[($i - 1) % count($notificationTemplates)];

            Notification::create([
                'user_id' => $user->id,
                'task_id' => $task ? $task->id : null,
                'title' => $tmpl['title'],
                'message' => $tmpl['body'],
                'read_at' => ($i % 3 === 0) ? now()->subHours(rand(1, 24)) : null,
            ]);
        }
    }
}
