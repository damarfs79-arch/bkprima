<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Classroom;
use App\Models\Major;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            if (!Schema::hasColumn('classrooms', 'jurusan_id')) {
                $table->foreignId('jurusan_id')->nullable()->after('level')->constrained('majors')->nullOnDelete();
            }
        });

        // Sinkronisasi jurusan_id dari major code yang ada
        $majors = Major::all()->keyBy('code');
        $firstGuru = User::where('role', 'guru')->first();

        $classrooms = Classroom::all();
        foreach ($classrooms as $c) {
            $updates = [];
            if (!empty($c->major) && isset($majors[$c->major])) {
                $updates['jurusan_id'] = $majors[$c->major]->id;
            }
            // Bersihkan default dummy data yang merujuk ke Admin IT Sekolah (User ID 1)
            if ($c->guru_bk_id === 1 || $c->guru_bk_name === 'Admin IT Sekolah') {
                $updates['guru_bk_id'] = $firstGuru ? $firstGuru->id : null;
                $updates['guru_bk_name'] = $firstGuru ? $firstGuru->name : null;
            }
            if (!empty($updates)) {
                $c->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            if (Schema::hasColumn('classrooms', 'jurusan_id')) {
                $table->dropForeign(['jurusan_id']);
                $table->dropColumn('jurusan_id');
            }
        });
    }
};
