<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // roles
        $roles = ['user', 'admin'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'is_main' => true]);
        }
        // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // permissions

        // role
        foreach (['create', 'show', 'edit', 'delete'] as $action) {
            Permission::firstOrCreate(['name' => $action.'_role', 'type' => 'roles_mng']);
        }

        // user
        foreach (['create', 'show', 'edit', 'delete'] as $action) {
            Permission::firstOrCreate(['name' => $action.'_user', 'type' => 'users_mng']);
        }

        // admin
        foreach (['create', 'show', 'edit', 'delete'] as $action) {
            Permission::firstOrCreate(['name' => $action.'_admin', 'type' => 'admins_mng']);
        }

    
        // main category
        foreach (['create', 'show', 'edit', 'delete'] as $action) {
            Permission::firstOrCreate(['name' => $action.'_main_category', 'type' => 'main_categories_mng']);
        }

        // sub category
        foreach (['create', 'show', 'edit', 'delete'] as $action) {
            Permission::firstOrCreate(['name' => $action.'_sub_category', 'type' => 'sub_categories_mng']);
        }
        // categories
        foreach (['create', 'show', 'edit', 'delete'] as $action) {
            Permission::firstOrCreate(['name' => $action.'_category', 'type' => 'categories_mng']);
        }

        // subcategories
        foreach (['create', 'show', 'edit', 'delete'] as $action) {
            Permission::firstOrCreate(['name' => $action.'_subcategory', 'type' => 'subcategories_mng']);
        }
      
        // payment-gateways
        foreach (['create', 'show', 'edit', 'delete'] as $action) {
            Permission::firstOrCreate(['name' => $action.'_payment_gateway', 'type' => 'payment_gateways_mng']);
        }

        // banners
        foreach (['create', 'show', 'edit', 'delete'] as $action) {
            Permission::firstOrCreate(['name' => $action.'_banner', 'type' => 'banners_mng']);
        }
        // faqs
        foreach (['create', 'show', 'edit', 'delete'] as $action) {
            Permission::firstOrCreate(['name' => $action.'_faq', 'type' => 'faqs_mng']);
        }
        // coupons
        foreach (['create', 'show', 'edit', 'delete'] as $action) {
            Permission::firstOrCreate(['name' => $action.'_coupon', 'type' => 'coupons_mng']);
        }
        // site-settings
        foreach (['show', 'edit'] as $action) {
            Permission::firstOrCreate(['name' => $action.'_site_setting', 'type' => 'site_settings_mng']);
        }
    }
}
