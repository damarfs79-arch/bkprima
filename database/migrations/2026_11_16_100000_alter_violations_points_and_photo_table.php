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
        Schema::table('violations', function (Blueprint $table) {
            $table->integer('points')->nullable()->default(null)->change();
            if (!Schema::hasColumn('violations', 'foto_bukti')) {
                $table->string('foto_bukti')->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('violations', function (Blueprint $table) {
            $table->integer('points')->default(5)->change();
            if (Schema::hasColumn('violations', 'foto_bukti')) {
                $table->dropColumn('foto_bukti');
            }
        });
    }
};
