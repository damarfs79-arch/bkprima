<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'rombel')) {
                $table->string('rombel', 20)->nullable()->after('class_name');
            }
            if (!Schema::hasColumn('students', 'graduation_year')) {
                $table->unsignedInteger('graduation_year')->nullable()->after('status');
            }
            if (!Schema::hasColumn('students', 'last_class_name')) {
                $table->string('last_class_name')->nullable()->after('graduation_year');
            }
            if (!Schema::hasColumn('students', 'academic_year')) {
                $table->string('academic_year', 20)->nullable()->default('2025/2026')->after('last_class_name');
            }
        });

        // Pastikan status siswa yang ada sekarang diubah menjadi 'aktif'
        DB::table('students')
            ->whereNotIn('status', ['lulus', 'mutasi'])
            ->update(['status' => 'aktif']);

        // Isi rombel jika belum terisi dari class_name (misal "X RPL 1" -> rombel "1")
        $students = DB::table('students')->get();
        foreach ($students as $st) {
            if (empty($st->rombel) && !empty($st->class_name)) {
                $parts = explode(' ', trim($st->class_name));
                $rombel = end($parts);
                DB::table('students')->where('id', $st->id)->update(['rombel' => $rombel]);
            }
        }

        // Tambahkan class_snapshot pada violations
        Schema::table('violations', function (Blueprint $table) {
            if (!Schema::hasColumn('violations', 'class_snapshot')) {
                $table->string('class_snapshot')->nullable()->after('student_class');
            }
        });
        DB::statement("UPDATE violations SET class_snapshot = student_class WHERE class_snapshot IS NULL OR class_snapshot = ''");

        // Tambahkan class_snapshot pada attendances
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'class_snapshot')) {
                $table->string('class_snapshot')->nullable()->after('student_id');
            }
        });
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("UPDATE attendances SET class_snapshot = (SELECT class_name FROM students WHERE students.id = attendances.student_id) WHERE class_snapshot IS NULL OR class_snapshot = ''");
        } else {
            DB::statement("UPDATE attendances a JOIN students s ON a.student_id = s.id SET a.class_snapshot = s.class_name WHERE a.class_snapshot IS NULL OR a.class_snapshot = ''");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['rombel', 'graduation_year', 'last_class_name', 'academic_year']);
        });

        Schema::table('violations', function (Blueprint $table) {
            $table->dropColumn(['class_snapshot']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['class_snapshot']);
        });
    }
};
