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
        Schema::create('submission_samples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('respondent_index'); // 1-10
            $table->unsignedTinyInteger('sample_index'); // 1-3
            $table->string('name')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();

            // Composite index for uniqueness per submission per respondent/sample
            $table->unique(['submission_id', 'respondent_index', 'sample_index'], 'submission_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_samples');
    }
};
