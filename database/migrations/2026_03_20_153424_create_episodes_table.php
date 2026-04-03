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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();

            $table->unsignedBigInteger('tmdb_id');
            $table->unsignedInteger('episode_number');

            $table->string('name');
            $table->text('overview')->nullable();
            $table->string('still_path')->nullable();
            $table->date('air_date')->nullable();

            $table->unsignedInteger('runtime')->nullable();

            $table->decimal('vote_average', 4, 2)->nullable();
            $table->unsignedInteger('vote_count')->default(0);

            $table->timestamps();

            $table->unique(['season_id', 'episode_number']);
            $table->unique('tmdb_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
