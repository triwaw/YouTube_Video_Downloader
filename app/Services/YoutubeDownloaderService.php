<?php

namespace App\Services;

use App\Models\Download;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class YoutubeDownloaderService
{
    public function download(
        Download $download
    ): bool
    {
        try {

            Download::where(
                'id',
                $download->id
            )->update([

                'status' => 'processing',

                'progress_percent' => 0,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Read Video Metadata
            |--------------------------------------------------------------------------
            */

            $infoProcess = new Process([

                'yt-dlp',

                '--dump-json',

                '--no-playlist',

                $download->youtube_url,
            ]);

            $infoProcess->setTimeout(300);

            $infoProcess->run();

            $videoInfo = [];

            if ($infoProcess->isSuccessful()) {

                $videoInfo = json_decode(
                    $infoProcess->getOutput(),
                    true
                ) ?? [];
            }

            $title = $videoInfo['title']
                ?? ('video_' . $download->id);

            $duration = $videoInfo['duration']
                ?? null;

            $thumbnail = $videoInfo['thumbnail']
                ?? null;

            /*
            |--------------------------------------------------------------------------
            | Safe Windows Filename
            |--------------------------------------------------------------------------
            */

            $safeTitle = preg_replace(

                '/[^\pL\pN\-_]+/u',

                '_',

                $title
            );

            $safeTitle = trim(
                $safeTitle,
                '_'
            );

            $safeTitle = Str::limit(

                $safeTitle,

                120,

                ''
            );

            /*
            |--------------------------------------------------------------------------
            | Output Template
            |--------------------------------------------------------------------------
            */

            $outputTemplate = storage_path(

                'app/private/videos/'
                . $safeTitle
                . '_'
                . $download->id
                . '.%(ext)s'
            );

            /*
            |--------------------------------------------------------------------------
            | Build yt-dlp Command
            |--------------------------------------------------------------------------
            */

            $command = [

                'yt-dlp',

                '--no-playlist',

                '--continue',

                '--newline',

                '--no-update',

                '--merge-output-format',
                'mp4',

                '-f',
                $download->format_id,

                '-o',
                $outputTemplate,

                $download->youtube_url,
            ];

            $process = new Process(
                $command
            );

            $process->setTimeout(
                7200
            );

            /*
            |--------------------------------------------------------------------------
            | Live Progress Parser
            |--------------------------------------------------------------------------
            */

            $process->run(function (

                $type,

                $buffer

            ) use ($download) {

                Log::info(
                    'yt-dlp output',
                    [
                        'buffer' => trim($buffer)
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | Progress %
                |--------------------------------------------------------------------------
                */

                if (

                    preg_match(

                        '/(\d+(?:\.\d+)?)%/',

                        $buffer,

                        $match
                    )
                ) {

                    Download::where(

                        'id',

                        $download->id

                    )->update([

                        'progress_percent'
                            => (float)$match[1],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Download Speed
                |--------------------------------------------------------------------------
                */

                if (

                    preg_match(

                        '/at\s+([^\s]+\/s)/',

                        $buffer,

                        $match
                    )
                ) {

                    Download::where(

                        'id',

                        $download->id

                    )->update([

                        'download_speed'
                            => $match[1],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | ETA
                |--------------------------------------------------------------------------
                */

                if (

                    preg_match(

                        '/ETA\s+([0-9:]+)/',

                        $buffer,

                        $match
                    )
                ) {

                    $parts = explode(
                        ':',
                        $match[1]
                    );

                    $seconds = 0;

                    if (
                        count($parts) == 2
                    ) {

                        $seconds =
                            ($parts[0] * 60)
                            + $parts[1];
                    }

                    Download::where(

                        'id',

                        $download->id

                    )->update([

                        'eta_seconds'
                            => $seconds,
                    ]);
                }
            });

            /*
            |--------------------------------------------------------------------------
            | Failed
            |--------------------------------------------------------------------------
            */

            if (

                !$process->isSuccessful()

            ) {

                Download::where(

                    'id',

                    $download->id

                )->update([

                    'status'
                        => 'failed',

                    'error_message'
                        => $process->getErrorOutput(),
                ]);

                Log::error(

                    'yt-dlp failed',

                    [

                        'download_id'
                            => $download->id,

                        'error'
                            => $process->getErrorOutput(),
                    ]
                );

                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | Locate Downloaded File
            |--------------------------------------------------------------------------
            */

            $files = glob(

                storage_path(

                    'app/private/videos/'
                    . $safeTitle
                    . '_'
                    . $download->id
                    . '.*'
                )
            );

            if (

                empty($files)

            ) {

                Download::where(

                    'id',

                    $download->id

                )->update([

                    'status'
                        => 'failed',

                    'error_message'
                        => 'Downloaded file not found.',
                ]);

                return false;
            }

            $finalFile = $files[0];

            /*
            |--------------------------------------------------------------------------
            | Save Metadata
            |--------------------------------------------------------------------------
            */

            Download::where(

                'id',

                $download->id

            )->update([

                'title'
                    => $title,

                'duration'
                    => $duration,

                'thumbnail_path'
                    => $thumbnail,

                'file_path'
                    => 'videos/'
                    . basename($finalFile),

                'file_size'
                    => filesize($finalFile),

                'progress_percent'
                    => 100,

                'download_speed'
                    => null,

                'eta_seconds'
                    => 0,

                'status'
                    => 'completed',

                'downloaded_at'
                    => now(),

                'expires_at'
                    => now()->addDays(2),
            ]);

            return true;

        } catch (\Throwable $e) {

            Log::error(

                'YoutubeDownloaderService Exception',

                [

                    'download_id'
                        => $download->id,

                    'message'
                        => $e->getMessage(),
                ]
            );

            Download::where(

                'id',

                $download->id

            )->update([

                'status'
                    => 'failed',

                'error_message'
                    => $e->getMessage(),
            ]);

            return false;
        }
    }
}