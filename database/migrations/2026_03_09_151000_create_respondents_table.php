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
        Schema::create('respondents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->onDelete('cascade');
            $table->string('nama_resp');
            $table->string('nks_resp')->nullable();
            $table->string('kec_sls')->nullable();
            $table->string('desa_sls')->nullable();
            $table->string('nama_sls')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();

            $table->index(['submission_id', 'nama_sls']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respondents');
    }
};
