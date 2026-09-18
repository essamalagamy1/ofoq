<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile_number')->unique()->nullable();
            $table->boolean('requires_password')->default(false);
            $table->string('type')->nullable(); // admin, teacher, parent
            $table->string('assigned_subject')->nullable(); // science, math, arabic (from enum)
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mobile_number', 'requires_password', 'type', 'assigned_subject']);
        });
    }
};
