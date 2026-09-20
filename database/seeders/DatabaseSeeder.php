<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $superadmin = User::firstOrCreate(
            ['phone' => '0123456789', 'phone_key' => '+966'],
            [
                'name' => 'superadmin',
                'password' => Hash::make('12345678'),
            ]
        );
        $superadmin->assignRole('admin')->givePermissionTo(Permission::all()->pluck('name')->toArray());
        $this->call(SiteSettingSeeder::class);
        $this->call(\Database\Seeders\OfoqSeeder::class);
    }
}
