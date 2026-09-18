<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $site = SiteSetting::create([
            'name' => [
                'ar' => 'منصة أفق',
                'en' => 'Ofoq Platform',
            ],
            'description' => [
                'ar' => 'منصة أفق التعليمية للتميز',
                'en' => 'Ofoq Educational Platform for Excellence',
            ],
        ]);
    }
}
