<?php

namespace App\Services;

use App\Models\Download;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class YoutubeDownloaderService
{
    /**
     * Download YouTube video
     */
    public function download(Download $download): bool
    {
        try {

            // Update status
            $download->update([
                'status' => 'processing',
            ]);

            // Generate unique filename
            $uniqueName = Str::uuid()->toString();

            // Output path
            $outputPath = storage_path(
                'app/private/videos/' . $uniqueName . '.%(ext)s'
            );

            /*
            |--------------------------------------------------------------------------
            | yt-dlp command
            |--------------------------------------------------------------------------
            */

			$command = [

				'yt-dlp',

				'--no-playlist',

				'--no-update',

				'--continue',

				'--newline',

				'--merge-output-format',
				'mp4',

				

				'-f',
				$download->format_id,

				'-o',
				$outputPath,

				$download->youtube_url,
			];
            /*
            |--------------------------------------------------------------------------
            | Run process
            |--------------------------------------------------------------------------
            */

            $process = new Process($command);

            $process->setTimeout(3600);

            $process->run();

            /*
            |--------------------------------------------------------------------------
            | Failed
            |--------------------------------------------------------------------------
            */

            if (!$process->isSuccessful()) {

                $download->update([

                    'status' => 'failed',

                    'error_message' => $process->getErrorOutput(),
                ]);

                Log::error('yt-dlp failed', [

                    'download_id' => $download->id,

                    'error' => $process->getErrorOutput(),
                ]);

                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | Find downloaded file
            |--------------------------------------------------------------------------
            */

            $files = glob(
                storage_path('app/private/videos/' . $uniqueName . '.*')
            );

            if (empty($files)) {

                $download->update([

                    'status' => 'failed',

                    'error_message' => 'Downloaded file not found.',
                ]);

                return false;
            }

            $finalFile = $files[0];

            /*
            |--------------------------------------------------------------------------
            | Save metadata
            |--------------------------------------------------------------------------
            */

            $download->update([

                'file_path' => basename($finalFile),

                'file_size' => filesize($finalFile),

                'status' => 'completed',

                'downloaded_at' => now(),

                'expires_at' => now()->addDays(2),
            ]);

            return true;

        } catch (\Throwable $e) {

            Log::error('YoutubeDownloaderService Exception', [

                'download_id' => $download->id,

                'message' => $e->getMessage(),
            ]);

            $download->update([

                'status' => 'failed',

                'error_message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}