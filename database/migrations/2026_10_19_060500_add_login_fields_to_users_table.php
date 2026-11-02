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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('role')->default('guru')->after('password');
            $table->string('nip')->nullable()->after('role');
            $table->string('nuptk')->nullable()->after('nip');
            $table->string('jabatan')->nullable()->after('nuptk');
            $table->string('phone')->nullable()->after('jabatan');
            $table->text('rombel')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'role',
                'nip',
                'nuptk',
                'jabatan',
                'phone',
                'rombel'
            ]);
        });
    }
};
