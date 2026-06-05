<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = [

        'user_id',

        'youtube_url',
        'youtube_id',

        'title',
        'description',

        'file_path',
        'thumbnail_path',

        'file_size',
        'duration',

        'format',
        'quality',

        'status',

        'error_message',

        'expires_at',
        'downloaded_at',

    'format_id',
    
    'progress_percent',
    'download_speed',
    'eta_seconds',
    'downloaded_bytes',


    ];

    protected $casts = [

        'expires_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];
}