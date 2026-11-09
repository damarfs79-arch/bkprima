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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. X RPL 1
            $table->string('level', 50)->default('Kelas 10 (X)');
            $table->string('major', 50)->default('RPL');
            $table->string('wali_kelas')->nullable();
            $table->foreignId('guru_bk_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guru_bk_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};

