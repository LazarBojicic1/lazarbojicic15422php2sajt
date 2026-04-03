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
        Schema::create('titles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id');
            $table->string('tmdb_type');
            $table->string('imdb_id')->nullable();

            $table->string('slug')->unique();
            $table->string('name');
            $table->string('original_name')->nullable();

            $table->text('overview')->nullable();

            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();

            $table->date('release_date')->nullable();
            $table->date('first_air_date')->nullable();
            $table->date('last_air_date')->nullable();

            $table->unsignedInteger('runtime')->nullable();
            $table->unsignedInteger('number_of_seasons')->nullable();
            $table->unsignedInteger('number_of_episodes')->nullable();

            $table->string('status')->nullable();
            $table->string('original_language', 10)->nullable();
            $table->string('country', 10)->nullable();

            $table->decimal('vote_average', 4, 2)->nullable();
            $table->unsignedInteger('vote_count')->default(0);
            $table->decimal('popularity', 8, 2)->nullable();

            $table->boolean('adult')->default(false);
            $table->boolean('is_published')->default(true);

            $table->timestamp('synced_at')->nullable();

            $table->timestamps();

            $table->unique(['tmdb_id', 'tmdb_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titles');
    }
};
