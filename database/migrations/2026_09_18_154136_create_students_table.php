<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nationality')->nullable();
            $table->integer('grade'); // 3 to 6
            $table->integer('semester'); // 1 to 3
            $table->string('parent_mobile_1')->nullable();
            $table->string('parent_mobile_2')->nullable();
            $table->string('parent_mobile_3')->nullable();
            $table->boolean('can_share_opinion')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
