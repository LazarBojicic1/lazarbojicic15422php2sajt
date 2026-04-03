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
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('title_id')->constrained('titles')->cascadeOnDelete();

            $table->unsignedBigInteger('tmdb_id');
            $table->unsignedInteger('season_number');

            $table->string('name');
            $table->text('overview')->nullable();
            $table->string('poster_path')->nullable();
            $table->date('air_date')->nullable();

            $table->unsignedInteger('episode_count')->default(0);

            $table->timestamps();

            $table->unique(['title_id', 'season_number']);
            $table->unique('tmdb_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
