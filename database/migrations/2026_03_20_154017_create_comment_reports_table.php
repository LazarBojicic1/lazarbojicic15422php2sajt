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
        Schema::create('comment_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('comment_id')->nullable()->constrained('comments')->nullOnDelete();
            $table->foreignId('reported_by_user_id')->constrained('users')->cascadeOnDelete();

            $table->string('reason')->nullable();
            $table->text('comment_snapshot')->nullable();
            $table->text('parent_comment_snapshot')->nullable();
            $table->string('comment_author_snapshot')->nullable();
            $table->string('title_snapshot')->nullable();
            $table->string('status')->default('pending');

            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_reports');
    }
};
