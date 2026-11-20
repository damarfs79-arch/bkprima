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
            if (Schema::hasColumn('students', 'nisn') && !Schema::hasColumn('students', 'nis')) {
                $table->renameColumn('nisn', 'nis');
            } elseif (!Schema::hasColumn('students', 'nis')) {
                $table->string('nis', 50)->nullable()->unique()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'nis') && !Schema::hasColumn('students', 'nisn')) {
                $table->renameColumn('nis', 'nisn');
            }
        });
    }
};
