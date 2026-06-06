<?php

namespace App\Jobs;

use App\Models\Download;
use App\Services\YoutubeDownloaderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class DownloadYoutubeVideoJob implements ShouldQueue
{
    use Queueable;

    /**
     * Large video downloads can take time.
     */
    public int $timeout = 7200;

    /**
     * Retry attempts.
     */
    public int $tries = 2;

    public Download $download;

    /**
     * Create a new job instance.
     */
    public function __construct(Download $download)
    {
        $this->download = $download;
    }

    /**
     * Execute the job.
     */
    public function handle(
        YoutubeDownloaderService $service
    ): void
    {
        $this->download->refresh();

        $this->download->update([

            'status' => 'processing',
        ]);

        $service->download(
            $this->download
        );
    }

    /**
     * Called automatically if job fails.
     */
    public function failed(
        Throwable $exception
    ): void
    {
        $this->download->refresh();

        $this->download->update([

            'status' => 'failed',

            'error_message' => $exception->getMessage(),
        ]);
    }
}