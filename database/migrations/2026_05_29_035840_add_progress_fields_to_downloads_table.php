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
        Schema::table('downloads', function (Blueprint $table) {

            // Download progress percentage
            $table->decimal('progress_percent', 5, 2)
                ->default(0)
                ->after('status');

            // Download speed text
            // Example: 2.3 MB/s
            $table->string('download_speed')
                ->nullable()
                ->after('progress_percent');

            // Estimated remaining seconds
            $table->integer('eta_seconds')
                ->nullable()
                ->after('download_speed');

            // Downloaded bytes so far
            $table->bigInteger('downloaded_bytes')
                ->default(0)
                ->after('eta_seconds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('downloads', function (Blueprint $table) {

            $table->dropColumn([
                'progress_percent',
                'download_speed',
                'eta_seconds',
                'downloaded_bytes',
            ]);
        });
    }
};