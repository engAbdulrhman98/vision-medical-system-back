<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Device;
use App\Models\MaintenanceReport;
use App\Models\Product;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $engineer = User::role('Service Engineer outdoor')->first() ?? $users->first();
        $tech = User::role('Service Engineer indoor')->first() ?? $users->first();
        $admin = User::role('Admin')->first() ?? $users->first();
        $product = Product::first();

        $clients = Client::with('contacts')->get();

        if ($clients->isEmpty()) {
            return;
        }

        // Seed 20 Devices first
        $devices = [];
        for ($i = 1; $i <= 20; $i++) {
            $client = $clients->random();
            $devices[] = Device::firstOrCreate(
                ['serial_number' => 'SN-MED-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT) . 'X'],
                [
                    'product_id' => $product ? $product->id : 1,
                    'client_id' => $client->id,
                    'status' => 'active',
                    'installation_date' => now()->subMonths(rand(1, 24)),
                ]
            );
        }

        $taskTemplates = [
            [
                'title' => ['ar' => 'معايرة جهاز السونار الفوق صوتي', 'en' => 'Ultrasound Scanner Calibration'],
                'desc' => 'إجراء فحص دقيق واستبدال المجس الرئيسي وضبط شدة الصورة.',
                'priority' => 'medium',
                'type' => 'external',
                'status' => 'completed',
                'progress' => 100,
            ],
            [
                'title' => ['ar' => 'صيانة طارئة لجهاز مراقبة المرضى ECG', 'en' => 'Emergency Repair for Patient Monitor'],
                'desc' => 'تغيير لوحة الشاشة الرئيسية وكابل التوصيل لاستعادة القراءات الحيوية.',
                'priority' => 'emergency',
                'type' => 'external',
                'status' => 'completed',
                'progress' => 100,
            ],
            [
                'title' => ['ar' => 'فحص ومعايرة جهاز التنفس الاصطناعي', 'en' => 'Ventilator Inspection & Calibration'],
                'desc' => 'تغيير الفلاتر واختبار معدل تدفق الأكسجين بغرفة العناية المركزة.',
                'priority' => 'high',
                'type' => 'external',
                'status' => 'in_progress',
                'progress' => 60,
            ],
            [
                'title' => ['ar' => 'زيارة مبيعات وعرض جهاز الرنين المغناطيسي', 'en' => 'Sales Demo & Inspection for MRI Unit'],
                'desc' => 'تقديم العرض الفني ومناقشة تفاصيل التجهيزات الكهربائية مع مهندس المستشفى.',
                'priority' => 'medium',
                'type' => 'external',
                'status' => 'pending',
                'progress' => 0,
            ],
            [
                'title' => ['ar' => 'إصلاح طلمبة المحاليل بالورشة المركزية', 'en' => 'Workshop Repair for Infusion Pump'],
                'desc' => 'فحص الموتور الداخلي واستبدال البطارية المدمجة واختبار السلامة الكهربائية.',
                'priority' => 'medium',
                'type' => 'internal',
                'status' => 'completed',
                'progress' => 100,
            ],
            [
                'title' => ['ar' => 'ترقية برمجيات جهاز الأشعة المقطعية CT', 'en' => 'CT Scanner Software Upgrade'],
                'desc' => 'تحديث حزمة البرامج التشغيلية ومعالجة خطأ معالجة الصور الثلاثية الأبعاد.',
                'priority' => 'high',
                'type' => 'internal',
                'status' => 'in_progress',
                'progress' => 40,
            ],
            [
                'title' => ['ar' => 'صيانة وقائية لجهاز الصدمات الكهربائية Defibrillator', 'en' => 'Preventive Maintenance for Defibrillator'],
                'desc' => 'قياس قدرة التفريغ الكهربائي واختبار كفاءة الشحن السريع.',
                'priority' => 'emergency',
                'type' => 'external',
                'status' => 'pending',
                'progress' => 0,
            ],
            [
                'title' => ['ar' => 'تركيب وتشغيل جهاز فحص الشبكية بالليزر', 'en' => 'Installation & Setup of Laser Ophthalmoscope'],
                'desc' => 'تركيب الجهاز بالعيادة الخارجية وتدريب الكادر الطبي على استخدامه.',
                'priority' => 'high',
                'type' => 'external',
                'status' => 'completed',
                'progress' => 100,
            ],
            [
                'title' => ['ar' => 'استبدال لمبة الإضاءة لغرفة العمليات', 'en' => 'Surgical Light Bulb Replacement'],
                'desc' => 'تركيب لمبة LED بديلة واختبار زوايا الإضاءة والتحكم في السطوع.',
                'priority' => 'low',
                'type' => 'external',
                'status' => 'completed',
                'progress' => 100,
            ],
            [
                'title' => ['ar' => 'فحص جهاز التخدير وتغيير المبخرة', 'en' => 'Anesthesia Machine Inspection & Vaporizer Change'],
                'desc' => 'إجراء الفحص السنوي وتعديل تسريب الغازات وضبط الصمامات.',
                'priority' => 'emergency',
                'type' => 'external',
                'status' => 'in_progress',
                'progress' => 75,
            ],
            [
                'title' => ['ar' => 'معايرة جهاز تحليل الدم الأوتوماتيكي', 'en' => 'Automated Blood Analyzer Calibration'],
                'desc' => 'تنظيف الأنابيب الدقيقة واختبار عينات المحاليل القياسية.',
                'priority' => 'medium',
                'type' => 'internal',
                'status' => 'pending',
                'progress' => 0,
            ],
            [
                'title' => ['ar' => 'تركيب كابلات الألياف الضوئية للمناظير', 'en' => 'Fiber Optic Cable Installation for Endoscopy'],
                'desc' => 'تركيب الكابلات وتجربة شدة الإضاءة ونقاء الصورة.',
                'priority' => 'medium',
                'type' => 'external',
                'status' => 'completed',
                'progress' => 100,
            ],
            [
                'title' => ['ar' => 'فحص دائرة التبريد لجهاز الماموجرام', 'en' => 'Cooling System Check for Mammography System'],
                'desc' => 'تزويد غاز التبريد وتنظيف السربنتينة الخارجية.',
                'priority' => 'low',
                'type' => 'external',
                'status' => 'pending',
                'progress' => 0,
            ],
            [
                'title' => ['ar' => 'صيانة جهاز كي جراحي الكتروني', 'en' => 'Electrosurgical Unit Service'],
                'desc' => 'استبدال دواسة القدم وضبط ترددات القطع والتجلط.',
                'priority' => 'high',
                'type' => 'internal',
                'status' => 'completed',
                'progress' => 100,
            ],
            [
                'title' => ['ar' => 'زيارة متابعة مبيعات لمستلزمات القسطرة', 'en' => 'Sales Follow-up Visit for Catheter Supplies'],
                'desc' => 'عرض الكتالوج الجديد واستلام طلبية توريد جديدة من مدير المستشفى.',
                'priority' => 'medium',
                'type' => 'external',
                'status' => 'completed',
                'progress' => 100,
            ],
            [
                'title' => ['ar' => 'فحص المحول الكهربائي لجهاز الأشعة المقطعية', 'en' => 'CT Transformer Voltage Testing'],
                'desc' => 'قياس استقرار الجهد واختبار المفاتيح الأوتوماتيكية.',
                'priority' => 'high',
                'type' => 'external',
                'status' => 'in_progress',
                'progress' => 50,
            ],
            [
                'title' => ['ar' => 'تغيير بطاريات نظام الطاقة غير المنقطعة UPS', 'en' => 'UPS Battery Replacement for Medical Unit'],
                'desc' => 'تركيب طقم بطاريات جافة جديد ومتابعة اختبار انقطاع التيار.',
                'priority' => 'medium',
                'type' => 'external',
                'status' => 'completed',
                'progress' => 100,
            ],
            [
                'title' => ['ar' => 'صيانة شاشة العرض لجهاز رسم المخ EEG', 'en' => 'EEG Display Monitor Maintenance'],
                'desc' => 'إصلاح كارت الشاشة بالورشة وإعادة المعايرة.',
                'priority' => 'low',
                'type' => 'internal',
                'status' => 'completed',
                'progress' => 100,
            ],
            [
                'title' => ['ar' => 'معايرة جهاز قياس نسبة الأكسجين بالدم', 'en' => 'Pulse Oximeter Calibration Visit'],
                'desc' => 'فحص كفاءة المجسات واستبدال التالف منها بالمركز الطبي.',
                'priority' => 'low',
                'type' => 'external',
                'status' => 'pending',
                'progress' => 0,
            ],
            [
                'title' => ['ar' => 'فحص وتغيير زيت مضخة الفراغ لغرفة التعقيم', 'en' => 'Autoclave Vacuum Pump Oil Change & Inspection'],
                'desc' => 'تغيير الزيت الهيدروليكي وضبط ضغط البخار بغرفة التعقيم المركزية.',
                'priority' => 'high',
                'type' => 'external',
                'status' => 'completed',
                'progress' => 100,
            ],
        ];

        foreach ($taskTemplates as $idx => $tmpl) {
            $client = $clients[$idx % $clients->count()];
            $contact = $client->contacts->isNotEmpty() ? $client->contacts->first() : null;
            $assignee = ($tmpl['type'] === 'external') ? $engineer : $tech;
            $device = $devices[$idx % count($devices)];

            $governorateId = null;
            $cityId = null;
            if ($client && $client->area) {
                if ($client->area->type === 'city') {
                    $cityId = $client->area->id;
                    $governorateId = $client->area->parent_id;
                } else if ($client->area->type === 'governorate') {
                    $governorateId = $client->area->id;
                }
            }

            // Distribute workflow statuses for rich testing
            $status = $tmpl['status'];
            if ($idx == 2) {
                $status = 'awaiting_accountant';
            } elseif ($idx == 3) {
                $status = 'accountant_ready';
            } elseif ($idx == 6) {
                $status = 'rejected';
            }

            $isCompleted = ($status === 'completed');
            $otpCode = str_pad((string)rand(1000, 9999), 4, '0', STR_PAD_LEFT);

            $task = Task::create([
                'title' => $tmpl['title'],
                'description' => $tmpl['desc'],
                'status' => $status,
                'progress' => $isCompleted ? 100 : ($status === 'accountant_ready' ? 80 : ($status === 'awaiting_accountant' ? 50 : $tmpl['progress'])),
                'priority' => $tmpl['priority'],
                'type' => $tmpl['type'],
                'action_type' => ($status === 'awaiting_accountant' || $status === 'accountant_ready') ? 'invoice_request' : 'quotation_request',
                'accountant_note' => $status === 'accountant_ready' ? 'تم تجهيز الفاتورة وسند التوريد والمحاسبة جاهزة بالتنسيق مع المستشفى' : null,
                'rejection_reason' => $status === 'rejected' ? 'تم رفض العرض لارتفاع ميزانية قطع الغيار المطلوبة' : null,
                'device_id' => $device ? $device->id : null,
                'client_id' => $client->id,
                'client_contact_id' => $contact ? $contact->id : null,
                'governorate_id' => $governorateId,
                'city_id' => $cityId,
                'user_id' => $assignee ? $assignee->id : $admin->id,
                'scheduled_at' => now()->addDays(rand(-10, 10)),
                'completed_at' => $isCompleted ? now()->subDays(rand(1, 5)) : null,
                'otp_code' => $otpCode,
                'otp_expires_at' => now()->addHours(2),
                'otp_verified_at' => $isCompleted ? now()->subDays(rand(1, 5)) : null,
            ]);

            // Add Task Updates
            if ($tmpl['progress'] > 0) {
                TaskUpdate::create([
                    'task_id' => $task->id,
                    'user_id' => $task->user_id,
                    'note' => 'تم البدء في فحص الجهاز والموقع والتأكد من متطلبات الصيانة.',
                    'progress' => min(50, $tmpl['progress']),
                ]);

                if ($isCompleted) {
                    TaskUpdate::create([
                        'task_id' => $task->id,
                        'user_id' => $task->user_id,
                        'note' => 'تم الانتهاء بنجاح وتأكيد وتوثيق إغلاق الطلب.',
                        'progress' => 100,
                    ]);
                }
            }

            // Add Maintenance Report for completed tasks
            if ($isCompleted) {
                MaintenanceReport::create([
                    'task_id' => $task->id,
                    'summary' => [
                        'ar' => 'تقرير صيانة شامل لـ ' . $tmpl['title']['ar'],
                        'en' => 'Maintenance summary for ' . $tmpl['title']['en'],
                    ],
                    'findings' => 'الجهاز كان يعاني من انحراف بسيط في المعايرة وتم ضُبط الإشارات الكهربائية.',
                    'actions_taken' => 'تم استبدال المكونات التالفة ومعايرة الجهاز وتسليمه بحالة ممتازة لمسؤول المستشفى.',
                    'status' => 'submitted',
                ]);
            }
        }
    }
}
