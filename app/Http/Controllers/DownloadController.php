<?php

namespace App\Http\Controllers;

use App\Jobs\DownloadYoutubeVideoJob;
use App\Models\Download;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Symfony\Component\Process\Process;

class DownloadController extends Controller
{
    /**
     * Dashboard
     */
    public function index()
    {
        $downloads = Download::where(
                'user_id',
                auth()->id()
            )
            ->latest()
            ->paginate(20);

        return view('downloads.index', [

            'downloads' => $downloads,
        ]);
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

            '--no-update',

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

                'youtube_url' => $process->getErrorOutput()
                    ?: 'Failed to analyze YouTube URL.',
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
                'format_id' =>
                    'bestvideo[height<=360]+bestaudio/best[height<=360]',
            ],

            [
                'label' => '480p MP4',
                'format_id' =>
                    'bestvideo[height<=480]+bestaudio/best[height<=480]',
            ],

            [
                'label' => '720p MP4',
                'format_id' =>
                    'bestvideo[height<=720]+bestaudio/best[height<=720]',
            ],

            [
                'label' => '1080p MP4',
                'format_id' =>
                    'bestvideo[height<=1080]+bestaudio/best[height<=1080]',
            ],

            [
                'label' => 'Best Available',
                'format_id' =>
                    'bestvideo+bestaudio/best',
            ],

            [
                'label' => 'Audio Only MP3',
                'format_id' =>
                    'bestaudio',
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

            'progress_percent' => 0,
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
        abort_unless(

            $download->user_id === auth()->id(),

            403
        );

        return view('downloads.show', [

            'download' => $download,
        ]);
    }

    /**
     * Download history
     */
    public function history()
    {
        return $this->index();
    }

    /**
     * Download completed file
     */
    public function file(Download $download)
    {
        abort_unless(

            $download->user_id === auth()->id(),

            403
        );

        if (
            !$download->file_path
        ) {

            abort(404);
        }

        $fullPath = storage_path(

            'app/private/videos/' .
            $download->file_path
        );

        if (!file_exists($fullPath)) {

            abort(404);
        }

        return response()->download(

            $fullPath,

            basename($download->file_path)
        );
    }

    /**
     * Delete download
     */
    public function destroy(Download $download)
    {
        abort_unless(

            $download->user_id === auth()->id(),

            403
        );

        if ($download->file_path) {

            $fullPath = storage_path(

                'app/private/videos/' .
                $download->file_path
            );

            if (file_exists($fullPath)) {

                unlink($fullPath);
            }
        }

        $download->delete();

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Download deleted successfully.'
            );
    }

    /**
     * AJAX status endpoint
     */
    public function status(Download $download)
    {
        abort_unless(

            $download->user_id === auth()->id(),

            403
        );

        $download->refresh();

        return response()->json([

            'id' => $download->id,

            'title' => $download->title,

            'status' => $download->status,

            'progress_percent' =>
                $download->progress_percent ?? 0,

            'download_speed' =>
                $download->download_speed,

            'eta_seconds' =>
                $download->eta_seconds,

            'downloaded_bytes' =>
                $download->downloaded_bytes,

            'file_size' =>
                $download->file_size,

            'file_path' =>
                $download->file_path,

            'error_message' =>
                $download->error_message,

            'downloaded_at' =>
                $download->downloaded_at,
        ]);
    }
}