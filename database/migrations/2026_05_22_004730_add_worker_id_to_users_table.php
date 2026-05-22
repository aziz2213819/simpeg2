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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('worker_id')->nullable()->constrained('workers')->nullOnDelete();
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_simpeg', 'admin_sampah', 'pegawai', 'petugas_sampah') DEFAULT 'pegawai'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['worker_id']);
            $table->dropColumn('worker_id');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_simpeg', 'admin_sampah', 'pegawai') DEFAULT 'pegawai'");
    }
};
