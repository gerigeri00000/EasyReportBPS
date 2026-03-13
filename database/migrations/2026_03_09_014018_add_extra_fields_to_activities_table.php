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
        Schema::table('activities', function (Blueprint $table) {
            $table->string('kecamatan')->nullable()->after('location');
            $table->string('desa')->nullable()->after('kecamatan');
            $table->string('nks_1')->nullable()->after('desa');
            $table->string('nks_2')->nullable()->after('nks_1');
            $table->string('nks_3')->nullable()->after('nks_2');
            $table->string('sls_1')->nullable()->after('nks_3');
            $table->string('sls_2')->nullable()->after('sls_1');
            $table->string('sls_3')->nullable()->after('sls_2');
            $table->string('nama_pemeriksa')->nullable()->after('sls_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn([
                'kecamatan',
                'desa',
                'nks_1',
                'nks_2',
                'nks_3',
                'sls_1',
                'sls_2',
                'sls_3',
                'nama_pemeriksa',
            ]);
        });
    }
};
