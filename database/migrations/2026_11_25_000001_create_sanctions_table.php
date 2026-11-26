<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sanctions')) {
            Schema::create('sanctions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            $now = now();
            $defaults = [
                ['name' => 'Pembinaan Lisan & Pengarahan BK', 'sort_order' => 1],
                ['name' => 'Piket Kebersihan Lingkungan Sekolah', 'sort_order' => 2],
                ['name' => 'Teguran Tertulis & Surat Perjanjian', 'sort_order' => 3],
                ['name' => 'Pemanggilan Orang Tua (SP-1)', 'sort_order' => 4],
                ['name' => 'Pemanggilan Orang Tua (SP-2)', 'sort_order' => 5],
                ['name' => 'Skorsing Sementara (SP-3)', 'sort_order' => 6],
            ];

            foreach ($defaults as $item) {
                DB::table('sanctions')->insert([
                    'name' => $item['name'],
                    'description' => null,
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sanctions');
    }
};
