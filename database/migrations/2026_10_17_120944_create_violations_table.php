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
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('cascade');
            $table->string('student_name');
            $table->string('student_class');
            $table->string('category');
            $table->string('violation_name');
            $table->integer('points')->default(5);
            $table->date('date');
            $table->string('time')->nullable();
            $table->string('reporter')->nullable();
            $table->string('penalty_action')->nullable();
            $table->string('status')->default('Tercatat');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};
