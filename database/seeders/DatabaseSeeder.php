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
            ['name' => 'د. خالد عبد الرحمن (CEO)', 'email' => 'ceo@example.com', 'role' => 'CEO', 'password' => 'password'],
            ['name' => 'م. أحمد علي (مدير النظام)', 'email' => 'admin@vision-medical.com', 'role' => 'Admin', 'password' => 'admin123'],
            ['name' => 'م. طارق المحمودي (مدير العمليات)', 'email' => 'operations@example.com', 'role' => 'Operations Manager', 'password' => 'password'],
            ['name' => 'م. أسامة مصطفى (مهندس صيانة ميدانية)', 'email' => 'engineer@example.com', 'role' => 'Service Engineer outdoor', 'password' => 'password'],
            ['name' => 'م. حسام الدين (فني الورشة المركزية)', 'email' => 'tech@example.com', 'role' => 'Service Engineer indoor', 'password' => 'password'],
            ['name' => 'م. عبد الله الشريف (مهندس موقع خارجي)', 'email' => 'outsourced@example.com', 'role' => 'Service Engineer outdoor', 'password' => 'password'],
            ['name' => 'أ. محمود جابر (المحاسب المالي)', 'email' => 'accountant@example.com', 'role' => 'Accountant', 'password' => 'password'],
            ['name' => 'أ. رانيا الباز (مديرة المستودع والمبيعات)', 'email' => 'inventory@example.com', 'role' => 'Sale', 'password' => 'password'],
            ['name' => 'م. ياسر صلاح (مهندس صيانة أجهزة تخدير)', 'email' => 'yasser.engineer@vision-medical.com', 'role' => 'Service Engineer outdoor', 'password' => 'password'],
            ['name' => 'م. هاني كمال (فني أجهزة سونار وأشعة)', 'email' => 'hany.tech@vision-medical.com', 'role' => 'Service Engineer indoor', 'password' => 'password'],
            ['name' => 'أ. سارة الحسيني (مسؤولة تحصيل وفواتير)', 'email' => 'sara.accountant@vision-medical.com', 'role' => 'Accountant', 'password' => 'password'],
            ['name' => 'أ. كريم عبد الله (مندوب مبيعات كبار العملاء)', 'email' => 'kareem.sales@vision-medical.com', 'role' => 'Sale', 'password' => 'password'],
            ['name' => 'م. دينا فهمي (مهندسة صيانة الرعاية المركزة)', 'email' => 'dina.engineer@vision-medical.com', 'role' => 'Service Engineer outdoor', 'password' => 'password'],
            ['name' => 'م. عمرو فوزي (فني معايرة ومعامل)', 'email' => 'amr.tech@vision-medical.com', 'role' => 'Service Engineer indoor', 'password' => 'password'],
            ['name' => 'م. بلال سلامة (مهندس جودة وضبط موقع)', 'email' => 'belal.engineer@vision-medical.com', 'role' => 'Service Engineer outdoor', 'password' => 'password'],
            ['name' => 'أ. نادية رشاد (مشرفة الدعم الفني)', 'email' => 'nadia.ops@vision-medical.com', 'role' => 'Operations Manager', 'password' => 'password'],
            ['name' => 'أ. حاتم نبيل (محاسب مبيعات ومشتريات)', 'email' => 'hatem.accountant@vision-medical.com', 'role' => 'Accountant', 'password' => 'password'],
            ['name' => 'أ. منى السيد (مسؤولة مبيعات الأجهزة التشخيصية)', 'email' => 'mona.sales@vision-medical.com', 'role' => 'Sale', 'password' => 'password'],
            ['name' => 'م. زياد العريان (مهندس صيانة مناظير وعيون)', 'email' => 'zeyad.engineer@vision-medical.com', 'role' => 'Service Engineer outdoor', 'password' => 'password'],
            ['name' => 'م. شريف نور (فني الورشة المركزية للأشعة)', 'email' => 'sherif.tech@vision-medical.com', 'role' => 'Service Engineer indoor', 'password' => 'password'],
        ];

        // Preserve default test@example.com user
        $defaultUser = User::where('email', 'test@example.com')->first();
        if (!$defaultUser) {
            $defaultUser = User::create([
                'name' => 'Test User Super Admin',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
            ]);
        }
        $defaultUser->assignRole('Admin');

        foreach ($employeesData as $emp) {
            $user = User::where('email', $emp['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $emp['name'],
                    'email' => $emp['email'],
                    'password' => Hash::make($emp['password']),
                ]);
            }
            $user->assignRole($emp['role']);
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
