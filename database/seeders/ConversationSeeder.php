<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clean existing conversations and messages to prevent duplicate seeds
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Message::truncate();
        Conversation::truncate();
        \Illuminate\Support\Facades\DB::table('conversation_user')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $users = User::with('roles')->get();

        if ($users->count() < 2) {
            return;
        }

        // Helper to find a user by role name or fallback
        $getUserByRole = function (string $roleName) use ($users) {
            $user = $users->first(function ($u) use ($roleName) {
                return $u->roles->pluck('name')->contains($roleName);
            });
            return $user ?: $users->random();
        };

        $ceo = $getUserByRole('CEO');
        $admin = $getUserByRole('Admin');
        $opsManager = $getUserByRole('Operations Manager');
        $engineerOutdoor = $getUserByRole('Service Engineer outdoor');
        $engineerIndoor = $getUserByRole('Service Engineer indoor');
        $accountant = $getUserByRole('Accountant');
        $sales = $getUserByRole('Sale');

        // Define conversations covering ALL roles in the system
        $roleConversations = [
            // 1. Operations Manager <-> Outdoor Engineer (Maintenance dispatch)
            [
                'user_a' => $opsManager,
                'user_b' => $engineerOutdoor,
                'subject' => 'متابعة صيانة سونار مستشفى الجيزة',
                'messages' => [
                    ['sender' => $opsManager, 'text' => 'أهلاً بشمهندس، هل تم البدء في معايرة جهاز السونار الفوق صوتي بمستشفى الجيزة الدولي؟'],
                    ['sender' => $engineerOutdoor, 'text' => 'أهلاً بك، نعم تم استبدال الكابل الرئيسي وفحص شاشة العرض، وجاري إنهاء المعايرة واستلام كود الـ OTP.'],
                    ['sender' => $opsManager, 'text' => 'ممتاز جداً، يرجى رفع تقرير الصيانة فور الانتهاء ليتم اعتماده.'],
                ]
            ],
            // 2. Indoor Engineer <-> Outdoor Engineer (Parts request)
            [
                'user_a' => $engineerIndoor,
                'user_b' => $engineerOutdoor,
                'subject' => 'طلب قطع غيار وكروت شاشة من الورشة المركزية',
                'messages' => [
                    ['sender' => $engineerOutdoor, 'text' => 'مساء الخير بشمهندس، محتاج كارت داتا لجهاز رسم القلب لعيادة مدينة نصر.'],
                    ['sender' => $engineerIndoor, 'text' => 'أهلاً يا هندسة، القطعة متوفرة حالياً بالورشة المركزية. يمكنك استلامها اليوم أو إرسال مندوب.'],
                    ['sender' => $engineerOutdoor, 'text' => 'تم، سأمر عليك بالورشة لاستلامها بعد معاايرة جهاز التخدير.'],
                ]
            ],
            // 3. Accountant <-> Sales (Invoice & quotation review)
            [
                'user_a' => $accountant,
                'user_b' => $sales,
                'subject' => 'طلب مراجعة عرض سعر ومستحقات مستشفى السلام',
                'messages' => [
                    ['sender' => $sales, 'text' => 'يرجى مراجعة الخصم المتاح لعرض سعر أجهزة مراقبة المرضى قبل إرساله للعميل.'],
                    ['sender' => $accountant, 'text' => 'تمت المراجعة والاعتماد المالي على النظام، يمكنك تصدير العرض وإرساله فوراً.'],
                    ['sender' => $sales, 'text' => 'شكراً جزيلاً، تم الإرسال وجاري متابعة التحصيل.'],
                ]
            ],
            // 4. CEO <-> Operations Manager (Management & strategy)
            [
                'user_a' => $ceo,
                'user_b' => $opsManager,
                'subject' => 'خطة الصيانة الوقائية الربع سنوية والميزانية',
                'messages' => [
                    ['sender' => $ceo, 'text' => 'يرجى إرسال تقرير خطة الصيانة الوقائية للشهور القادمة لمراجعة الميزانية التشغيلية.'],
                    ['sender' => $opsManager, 'text' => 'تم إعداد خطة الزيارات الميدانية لكل المحافظات، وجاري مشاركتها مع سيادتكم على النظام.'],
                    ['sender' => $ceo, 'text' => 'عظيم جداً، برجاء التركيز على أجهزة الرعاية المركزة بمستشفيات القاهرة والجيزة.'],
                ]
            ],
            // 5. Admin <-> CEO (System admin report)
            [
                'user_a' => $admin,
                'user_b' => $ceo,
                'subject' => 'تحديث الصلاحيات والأمان في نظام فيجن ميديكال',
                'messages' => [
                    ['sender' => $admin, 'text' => 'تم تحديث مصفوفة الصلاحيات وتفعيل بروتوكولات الأمان لجميع مستخدمي النظام.'],
                    ['sender' => $ceo, 'text' => 'جهد مشكور، هل تم ربط جميع الموظفين بمختلف الأدوار بنظام المحادثات المباشرة؟'],
                    ['sender' => $admin, 'text' => 'نعم يا فندم، جميع الأدوار (CEO, Admin, Operations, Engineers, Accountant, Sales) تعمل بكفاءة.'],
                ]
            ],
            // 6. Outdoor Engineer <-> Sales (Client equipment request)
            [
                'user_a' => $engineerOutdoor,
                'user_b' => $sales,
                'subject' => 'استفسار عميل عن توفر ملحقات أجهزة التخدير',
                'messages' => [
                    ['sender' => $sales, 'text' => 'يا هندسة، عميل مستشفى الصفوة بيسأل عن إمكانية توريد كابلات إضافية لجهاز التخدير.'],
                    ['sender' => $engineerOutdoor, 'text' => 'المواصفات الفنية مطابقة ومتاحة، بلّغ العميل إني هعمل فحص للموقع يوم الثلاثاء.'],
                ]
            ],
            // 7. CEO <-> Accountant (Financial overview)
            [
                'user_a' => $ceo,
                'user_b' => $accountant,
                'subject' => 'مراجعة التحصيلات والتدفقات المالية للشهر الحالي',
                'messages' => [
                    ['sender' => $ceo, 'text' => 'أ. محمود، يرجى تزويدي بموقف الفواتير المحصلة لعقود الصيانة السنوية.'],
                    ['sender' => $accountant, 'text' => 'تم تحصيل 85% من إجمالي العقود، وجاري متابعة باقي المبالغ مع المستشفيات الخاصة.'],
                ]
            ],
            // 8. Admin <-> Indoor Engineer (Technical support & software)
            [
                'user_a' => $admin,
                'user_b' => $engineerIndoor,
                'subject' => 'تحديث برامج الفحص والتشخيص للورشة المركزية',
                'messages' => [
                    ['sender' => $admin, 'text' => 'تم رفع تحديث برامج معايرة السونار والأشعة على خادم الورشة.'],
                    ['sender' => $engineerIndoor, 'text' => 'تم التحميل واختبار البرمجيات، النتيجة ممتازة والدقة عالية جداً.'],
                ]
            ]
        ];

        foreach ($roleConversations as $chat) {
            $sender = $chat['user_a'];
            $receiver = $chat['user_b'];

            // Ensure sender and receiver are not the exact same user instance
            if ($sender->id === $receiver->id) {
                $otherUsers = $users->filter(fn($u) => $u->id !== $sender->id);
                if ($otherUsers->count() > 0) {
                    $receiver = $otherUsers->first();
                }
            }

            $conversation = Conversation::create([
                'is_group' => false,
                'name' => $chat['subject'],
            ]);

            $conversation->users()->attach([$sender->id, $receiver->id]);

            foreach ($chat['messages'] as $mIdx => $msg) {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $msg['sender']->id,
                    'body' => $msg['text'],
                    'read_at' => ($mIdx === count($chat['messages']) - 1) ? null : now()->subMinutes(rand(10, 180)),
                ]);
            }
        }
    }
}
