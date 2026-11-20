<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'classroom_id')) {
                $table->foreignId('classroom_id')
                    ->nullable()
                    ->after('class_name')
                    ->constrained('classrooms')
                    ->nullOnDelete();
            }
        });

        // Sinkronisasi data existing students dengan classrooms berdasarkan nama
        $classrooms = DB::table('classrooms')->get();
        foreach ($classrooms as $c) {
            DB::table('students')
                ->where('class_name', $c->name)
                ->whereNull('classroom_id')
                ->update(['classroom_id' => $c->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'classroom_id')) {
                $table->dropForeign(['classroom_id']);
                $table->dropColumn('classroom_id');
            }
        });
    }
};
