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
        $users = User::all();

        if ($users->count() < 2) {
            return;
        }

        $chatTemplates = [
            [
                'subject' => 'متابعة صيانة سونار مستشفى الجيزة',
                'messages' => [
                    'أهلاً بشمهندس، هل تم البدء في معايرة جهاز السونار الفوق صوتي بمستشفى الجيزة الدولي؟',
                    'أهلاً بك، نعم تم استبدال الكابل الرئيسي وفحص شاشة العرض، وجاري إنهاء المعايرة واستلام كود الـ OTP.',
                    'ممتاز جداً، يرجى رفع تقرير الصيانة فور الانتهاء ليتم اعتماده.',
                ]
            ],
            [
                'subject' => 'استلام طلبية مستلزمات العناية المركزة',
                'messages' => [
                    'مساء الخير، تم وصول الشحنة الجديدة من فلاتر أجهزة التنفس الاصطناعي للمستودع.',
                    'ممتاز، سأقوم بتسجيل الكميات في نظام المخزون وإبلاغ فريق الصيانة الميدانية.',
                ]
            ],
            [
                'subject' => 'طلب مراجعة عرض سعر مستشفى السلام',
                'messages' => [
                    'يرجى مراجعة الخصم المتاح لعرض سعر أجهزة مراقبة المرضى قبل إرساله للعميل.',
                    'تمت المراجعة والتعديل على النظام، يمكنك تصدير العرض الآن.',
                ]
            ],
            [
                'subject' => 'التنسيق لزيارة طارئة لعيادة مدينة نصر',
                'messages' => [
                    'العميل يبلغ عن خطأ في قراءة جهاز رسم القلب، يرجى التوجه للموقع فوراً.',
                    'علم، تم التواصل مع مسؤول المستشفى وأنا في الطريق إليهم الآن.',
                    'تمت الزيارة واستبدال الكابل التالف واختبار الجهاز بحالة جيدة.',
                ]
            ],
            [
                'subject' => 'جدول الصيانة الوقائية الربع سنوي',
                'messages' => [
                    'يرجى إرسال جدول خطة الصيانة الوقائية للشهور القادمة لمراجعة الميزانية.',
                    'تم إعداد خطة الزيارات الميدانية لكل المحافظات وسيتم مشاركتها معك على النظام.',
                ]
            ],
        ];

        foreach ($chatTemplates as $idx => $chat) {
            $sender = $users[$idx % $users->count()];
            $receiver = $users[($idx + 1) % $users->count()];

            $conversation = Conversation::create([
                'is_group' => false,
                'name' => $chat['subject'],
            ]);

            $conversation->users()->attach([$sender->id, $receiver->id]);

            foreach ($chat['messages'] as $mIdx => $msgText) {
                $author = ($mIdx % 2 === 0) ? $sender : $receiver;

                Message::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $author->id,
                    'body' => $msgText,
                    'read_at' => ($mIdx === count($chat['messages']) - 1) ? null : now()->subMinutes(rand(10, 120)),
                ]);
            }
        }
    }
}
