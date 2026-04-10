<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@computersgame.org'],
            [
                'name'     => 'مدير الموقع',
                'password' => Hash::make('Admin@12345'),
                'role'     => 'admin',
                'status'   => 'active',
            ]
        );

        $this->command->info('✓ تم إنشاء حساب المدير: admin@computersgame.org');
        $this->command->warn('⚠ غيّر كلمة المرور فور تسجيل الدخول الأول!');
    }
}
