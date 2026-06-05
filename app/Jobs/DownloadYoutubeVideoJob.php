<?php

namespace App\Jobs;

use App\Models\Download;
use App\Services\YoutubeDownloaderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DownloadYoutubeVideoJob implements ShouldQueue
{
    use Queueable;

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
        $service->download($this->download);
    }
}