<?php

namespace Database\Seeders;

use App\Models\AcademicCycle;
use App\Models\BadgeSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class OfoqSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roles = ['super_admin', 'teacher', 'parent'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // 2. Super Admin User
        $admin = User::firstOrCreate(
            ['email' => 'superadmin@admin.com'],
            [
                'name' => 'Super Admin',
                'mobile_number' => '01000000000',
                'password' => Hash::make('12345678'),
                'requires_password' => true,
                'type' => 'admin',
            ]
        );
        $admin->assignRole('super_admin');

        // 3. Initial Academic Cycle
        AcademicCycle::firstOrCreate(
            ['is_active' => true],
            ['name' => 'الدورة الأكاديمية الأولى']
        );

        // 4. Default Badge Settings
        $badges = [
            ['name' => 'برونزي', 'min_percentage' => 0, 'max_percentage' => 50, 'color_hex' => '#cd7f32'],
            ['name' => 'فضي', 'min_percentage' => 51, 'max_percentage' => 84, 'color_hex' => '#c0c0c0'],
            ['name' => 'ذهبي', 'min_percentage' => 85, 'max_percentage' => 100, 'color_hex' => '#ffd700'],
        ];

        foreach ($badges as $badge) {
            BadgeSetting::firstOrCreate(['name' => $badge['name']], $badge);
        }
    }
}
