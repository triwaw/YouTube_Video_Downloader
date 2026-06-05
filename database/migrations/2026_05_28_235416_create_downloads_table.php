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
        Schema::create('downloads', function (Blueprint $table) {

            $table->id();

            // Owner
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Original YouTube URL
            $table->text('youtube_url');

            // YouTube video ID
            $table->string('youtube_id')->nullable()->index();

            // Video metadata
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            // Storage
            $table->string('file_path')->nullable();
            $table->string('thumbnail_path')->nullable();

            // Media info
            $table->bigInteger('file_size')->nullable();
            $table->integer('duration')->nullable();

            // Format
            $table->string('format')->default('mp4');

            // Resolution
            $table->string('quality')->nullable();

            // Download status
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'expired',
                'deleted'
            ])->default('pending');

            // Error logs
            $table->text('error_message')->nullable();

            // Expiration cleanup
            $table->timestamp('expires_at')->nullable();

            // Download completed timestamp
            $table->timestamp('downloaded_at')->nullable();

            $table->timestamps();

            // Useful indexes
            $table->index('status');
            $table->index('expires_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};