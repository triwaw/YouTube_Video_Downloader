<?php

namespace App\Http\Controllers;

use App\Jobs\DownloadYoutubeVideoJob;
use App\Models\Download;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class DownloadController extends Controller
{
    /**
     * Dashboard
     */
    public function index()
    {
        return view('downloads.index');
    }

    /**
     * Analyze YouTube URL
     */
    public function analyze(Request $request)
    {
        $request->validate([

            'youtube_url' => [
                'required',
                'url',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Get metadata using yt-dlp JSON mode
        |--------------------------------------------------------------------------
        */

        $process = new Process([

            'yt-dlp',

            '-J',

            '--no-playlist',

            $request->youtube_url,
        ]);

        $process->setTimeout(120);

        $process->run();

        /*
        |--------------------------------------------------------------------------
        | Failed
        |--------------------------------------------------------------------------
        */

        if (!$process->isSuccessful()) {

            return back()->withErrors([

                'youtube_url' => 'Failed to analyze YouTube URL.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Parse JSON
        |--------------------------------------------------------------------------
        */

        $video = json_decode(
            $process->getOutput(),
            true
        );

        if (!$video) {

            return back()->withErrors([

                'youtube_url' => 'Invalid response from yt-dlp.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Build format list
        |--------------------------------------------------------------------------
        */

        $formats = [

				[
					'label' => '360p MP4',
					'format_id' => 'bestvideo[height<=360]+bestaudio/best[height<=360]',
				],

				[
					'label' => '480p MP4',
					'format_id' => 'bestvideo[height<=480]+bestaudio/best[height<=480]',
				],

				[
					'label' => '720p MP4',
					'format_id' => 'bestvideo[height<=720]+bestaudio/best[height<=720]',
				],

				[
					'label' => '1080p MP4',
					'format_id' => 'bestvideo[height<=1080]+bestaudio/best[height<=1080]',
				],

				[
					'label' => 'Best Available',
					'format_id' => 'bestvideo+bestaudio/best',
				],

				[
					'label' => 'Audio Only MP3',
					'format_id' => 'bestaudio',
				],
			];

        return view('downloads.analyze', [

            'video' => $video,

            'formats' => $formats,

            'youtube_url' => $request->youtube_url,
        ]);
    }

    /**
     * Store download request
     */
    public function store(Request $request)
    {
        $request->validate([

            'youtube_url' => 'required|url',

            'format_id' => 'required|string',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create DB record
        |--------------------------------------------------------------------------
        */

        $download = Download::create([

            'user_id' => auth()->id(),

            'youtube_url' => $request->youtube_url,

            'format_id' => $request->format_id,

            'status' => 'pending',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Dispatch Queue Job
        |--------------------------------------------------------------------------
        */

        DownloadYoutubeVideoJob::dispatch($download);

        return redirect()->route(
            'downloads.show',
            $download
        );
    }

    /**
     * Monitor download
     */
    public function show(Download $download)
    {
        return view('downloads.show', [

            'download' => $download,
        ]);
    }
}