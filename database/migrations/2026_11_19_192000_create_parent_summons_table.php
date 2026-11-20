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
        Schema::create('parent_summons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('student_name')->nullable();
            $table->string('nis')->nullable();
            $table->string('class_name')->nullable();
            $table->string('letter_number')->nullable();
            $table->string('stage')->default('SP-1'); // Input bebas: SP-1, SP-2, Panggilan 1, dll.
            $table->string('case_title');
            $table->text('case_description')->nullable();
            $table->integer('points')->nullable()->default(0);
            $table->string('meeting_date')->nullable();
            $table->string('meeting_time')->nullable();
            $table->string('room')->default('Ruang BK');
            $table->string('parent_name')->nullable();
            $table->string('parent_phone')->nullable();
            $table->string('status')->default('Menunggu Kehadiran'); // Menunggu Kehadiran, Mediasi Selesai, Dibatalkan
            $table->string('counselor')->nullable();
            $table->text('mediation_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_summons');
    }
};
