<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Video Downloader 

Youtube Video  Dowloader is a web application  with expressive, elegant syntax. 

## Contributing

Thank you for considering using or contributing to Youtube Video  Dowloader ! 

## Code of Conduct

In order to ensure that the Youtube Video  Dowloader  is welcoming to all, please review and abide by the 

## Technology Stack

- Laravel 12
- PHP 8.2+
- MySQL / MariaDB
- Filament 4
- Livewire
- AlpineJS


# Installation

git clone ...

composer install

npm install

cp .env.example .env

php artisan key:generate

php artisan migrate

npm run build


## Requirements

- PHP 8.2+
- Composer 2.x
- Node.js 22+
- FFmpeg
- yt-dlp
- MySQL 8+
Issues Labels


## Changes Required when you clone this repository 

Replace Database Connection setting in .env file ;  I used MySQL  instead of SQLite 

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=udownloader
DB_USERNAME=root
DB_PASSWORD=


##   Create labels:

bug
enhancement
ui
backend
download-engine
ffmpeg
yt-dlp

##   Issues for Planned Features 
#1 Auto Refresh Download Progress
#2 Playlist Download Support
#3 Download History
#4 Background Queue Processing
#5 Audio-Only Download Mode
#6 Download Retry Logic

##  License

The YouTuibe Video Downloader is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
"# YouTube_Video_Downloader" 

## Roadmap

- [x] Basic YouTube download
- [x] Audio merge
- [x] Progress tracking
- [ ] Playlist download
- [ ] Download history
- [ ] Scheduled downloads
- [ ] Multi-language UI
- [ ] Android version

##  Architecture Diagram

A simple diagram helps future-you.

Browser
   ↓
Laravel Controller
   ↓
Job Queue
   ↓
yt-dlp
   ↓
FFmpeg
   ↓
Storage

##  Version 2  Features 

✔ Laravel Authentication (Breeze)
✔ Download Database
✔ Download Queue Jobs
✔ yt-dlp Integration
✔ FFmpeg Integration
✔ Video Analysis Screen
✔ Resolution Selection
✔ Download History
✔ File Download
✔ Real-time AJAX Status Polling
✔ Progress Database Fields
✔ Video Title as Filename
✔ Background Processing via Queue Worker
✔ GitHub Source Control

Version 2:
- Clone instructions
- Planned features

Milestone 3:
- AJAX polling
- Real-time monitor

##  code block

```bash
git clone https://github.com/triwaw/youDownload.git

