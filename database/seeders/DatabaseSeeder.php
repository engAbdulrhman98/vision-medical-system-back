<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\ClientSeeder;
use Database\Seeders\ConversationSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call(RolesAndPermissionsSeeder::class);

        // Define 20 realistic employee accounts across all roles
        $employeesData = [
            ['name' => 'د. خالد عبد الرحمن (CEO)', 'email' => 'ceo@example.com', 'username' => 'ceo', 'role' => 'CEO', 'password' => 'password'],
            ['name' => 'م. أحمد علي (مدير النظام)', 'email' => 'admin@vision-medical.com', 'username' => 'admin', 'role' => 'Admin', 'password' => 'admin123'],
            ['name' => 'م. طارق المحمودي (مدير العمليات)', 'email' => 'operations@example.com', 'username' => 'operations', 'role' => 'Operations Manager', 'password' => 'password'],
            ['name' => 'م. أسامة مصطفى (مهندس صيانة ميدانية)', 'email' => 'engineer@example.com', 'username' => 'engineer', 'role' => 'Service Engineer outdoor', 'password' => 'password'],
            ['name' => 'م. حسام الدين (فني الورشة المركزية)', 'email' => 'tech@example.com', 'username' => 'tech', 'role' => 'Service Engineer indoor', 'password' => 'password'],
            ['name' => 'م. عبد الله الشريف (مهندس موقع خارجي)', 'email' => 'outsourced@example.com', 'username' => 'outsourced', 'role' => 'Service Engineer outdoor', 'password' => 'password'],
            ['name' => 'أ. محمود جابر (المحاسب المالي)', 'email' => 'accountant@example.com', 'username' => 'accountant', 'role' => 'Accountant', 'password' => 'password'],
            ['name' => 'أ. سامح عبد الفتاح (مسؤول التحصيل والخزينة)', 'email' => 'collector@example.com', 'username' => 'collector', 'role' => 'Collector', 'password' => 'password'],
            ['name' => 'أ. رانيا الباز (مديرة المستودع والمبيعات)', 'email' => 'inventory@example.com', 'username' => 'seller', 'role' => 'Sale', 'password' => 'password'],
            ['name' => 'م. ياسر صلاح (مهندس صيانة أجهزة تخدير)', 'email' => 'yasser.engineer@vision-medical.com', 'username' => 'yasser', 'role' => 'Service Engineer outdoor', 'password' => 'password'],
            ['name' => 'م. هاني كمال (فني أجهزة سونار وأشعة)', 'email' => 'hany.tech@vision-medical.com', 'username' => 'hany', 'role' => 'Service Engineer indoor', 'password' => 'password'],
            ['name' => 'أ. سارة الحسيني (مسؤولة تحصيل وفواتير)', 'email' => 'sara.accountant@vision-medical.com', 'username' => 'sara', 'role' => 'Collector', 'password' => 'password'],
            ['name' => 'أ. كريم عبد الله (مندوب مبيعات كبار العملاء)', 'email' => 'kareem.sales@vision-medical.com', 'username' => 'kareem', 'role' => 'Sale', 'password' => 'password'],
            ['name' => 'م. دينا فهمي (مهندسة صيانة الرعاية المركزة)', 'email' => 'dina.engineer@vision-medical.com', 'username' => 'dina', 'role' => 'Service Engineer outdoor', 'password' => 'password'],
            ['name' => 'م. عمرو فوزي (فني معايرة ومعامل)', 'email' => 'amr.tech@vision-medical.com', 'username' => 'amr', 'role' => 'Service Engineer indoor', 'password' => 'password'],
            ['name' => 'م. بلال سلامة (مهندس جودة وضبط موقع)', 'email' => 'belal.engineer@vision-medical.com', 'username' => 'belal', 'role' => 'Service Engineer outdoor', 'password' => 'password'],
            ['name' => 'أ. نادية رشاد (مشرفة الدعم الفني)', 'email' => 'nadia.ops@vision-medical.com', 'username' => 'nadia', 'role' => 'Operations Manager', 'password' => 'password'],
            ['name' => 'أ. حاتم نبيل (محاسب مبيعات ومشتريات)', 'email' => 'hatem.accountant@vision-medical.com', 'username' => 'hatem', 'role' => 'Accountant', 'password' => 'password'],
            ['name' => 'أ. منى السيد (مسؤولة مبيعات الأجهزة التشخيصية)', 'email' => 'mona.sales@vision-medical.com', 'username' => 'mona', 'role' => 'Sale', 'password' => 'password'],
            ['name' => 'م. زياد العريان (مهندس صيانة مناظير وعيون)', 'email' => 'zeyad.engineer@vision-medical.com', 'username' => 'zeyad', 'role' => 'Service Engineer outdoor', 'password' => 'password'],
            ['name' => 'م. شريف نور (فني الورشة المركزية للأشعة)', 'email' => 'sherif.tech@vision-medical.com', 'username' => 'sherif', 'role' => 'Service Engineer indoor', 'password' => 'password'],
        ];

        // Preserve default test@example.com user
        $defaultUser = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User Super Admin',
                'username' => 'test_admin',
                'password' => Hash::make('password'),
            ]
        );
        try { $defaultUser->assignRole('Admin'); } catch (\Throwable $t) {}

        foreach ($employeesData as $emp) {
            $user = User::updateOrCreate(
                ['email' => $emp['email']],
                [
                    'name' => $emp['name'],
                    'username' => $emp['username'],
                    'password' => Hash::make($emp['password']),
                ]
            );
            try { $user->assignRole($emp['role']); } catch (\Throwable $t) {}
        }

        // Call all seeded modules
        $this->call([
            SettingSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            ContactMessageSeeder::class,
            AreaSeeder::class,
            ClientSeeder::class,
            // QuotationSeeder::class, // Hidden as per user request
            NotificationSeeder::class,
            ConversationSeeder::class,
            TaskSeeder::class,
        ]);
    }
}
