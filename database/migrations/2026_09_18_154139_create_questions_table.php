<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cycle_id')->constrained('academic_cycles')->cascadeOnDelete();
            $table->string('subject');
            $table->integer('grade'); // 3 to 6
            $table->integer('week'); // 1 to 12
            $table->text('content');
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->string('correct_option'); // A, B, C, D
            $table->boolean('is_parent_suggestion')->default(false);
            $table->string('status')->default('approved'); // pending, approved, rejected
            $table->text('teacher_comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
