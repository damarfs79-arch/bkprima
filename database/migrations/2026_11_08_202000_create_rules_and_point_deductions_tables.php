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
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('pelanggaran'); // pelanggaran / apresiasi
            $table->string('category'); // Ringan, Sedang, Berat, Apresiasi, Prestasi
            $table->string('code')->nullable();
            $table->string('name');
            $table->integer('points')->default(5);
            $table->string('sanction')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('point_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('rule_id')->nullable()->constrained('rules')->nullOnDelete();
            $table->string('title');
            $table->string('type')->default('tugas_pembinaan'); // tugas_pembinaan, prestasi, konseling_tuntas, lainnya
            $table->integer('points_deducted')->default(5);
            $table->date('date');
            $table->string('officer')->nullable();
            $table->text('notes')->nullable();
            $table->string('evidence_photo_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_deductions');
        Schema::dropIfExists('rules');
    }
};

