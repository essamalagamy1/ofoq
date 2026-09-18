<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('parent_mobile_1_key')->nullable()->after('parent_mobile_1');
            $table->string('parent_mobile_2_key')->nullable()->after('parent_mobile_2');
            $table->string('parent_mobile_3_key')->nullable()->after('parent_mobile_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'parent_mobile_1_key',
                'parent_mobile_2_key',
                'parent_mobile_3_key',
            ]);
        });
    }
};
