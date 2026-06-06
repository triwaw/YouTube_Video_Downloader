<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Download Monitor
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                <h1
                    id="videoTitle"
                    class="text-2xl font-bold mb-6"
                >
                    {{ $download->title ?? 'YouTube Download' }}
                </h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <strong>ID:</strong>
                        {{ $download->id }}
                    </div>

                    <div>

                        <strong>Status:</strong>

                        <span
                            id="statusText"
                            class="font-bold"
                        >
                            {{ ucfirst($download->status) }}
                        </span>

                    </div>

                    <div>

                        <strong>Quality:</strong>

                        {{ $download->quality ?? 'N/A' }}

                    </div>

                    <div>

                        <strong>Format:</strong>

                        {{ $download->format ?? 'N/A' }}

                    </div>

                </div>

                <hr class="my-6">

                <div>

                    <div class="flex justify-between mb-2">

                        <span>
                            Progress
                        </span>

                        <span id="progressText">

                            {{ number_format($download->progress_percent ?? 0, 2) }} %

                        </span>

                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-6">

                        <div
                            id="progressBar"
                            class="bg-green-600 h-6 rounded-full text-white text-center text-sm"
                            style="width: {{ $download->progress_percent ?? 0 }}%;"
                        >

                            {{ round($download->progress_percent ?? 0) }}%

                        </div>

                    </div>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

                    <div>

                        <strong>Speed</strong>

                        <br>

                        <span id="speedText">

                            {{ $download->download_speed ?? 'N/A' }}

                        </span>

                    </div>

                    <div>

                        <strong>ETA</strong>

                        <br>

                        <span id="etaText">

                            @if($download->eta_seconds)

                                {{ gmdate('H:i:s', $download->eta_seconds) }}

                            @else

                                N/A

                            @endif

                        </span>

                    </div>

                    <div>

                        <strong>File Size</strong>

                        <br>

                        <span id="fileSizeText">

                            @if($download->file_size)

                                {{ number_format($download->file_size / 1024 / 1024, 2) }} MB

                            @else

                                N/A

                            @endif

                        </span>

                    </div>

                </div>

                <div
                    id="errorContainer"
                    class="hidden mt-6 p-4 bg-red-100 border border-red-300 rounded text-red-700"
                ></div>

                @if($download->error_message)

                    <div
                        class="mt-6 p-4 bg-red-100 border border-red-300 rounded text-red-700"
                    >

                        <strong>Error:</strong>

                        <pre class="whitespace-pre-wrap text-sm mt-2">
{{ $download->error_message }}
                        </pre>

                    </div>

                @endif

                <div
                    id="downloadArea"
                    class="mt-6"
                >

                    @if($download->status === 'completed')

                        <div
                            class="p-4 bg-green-100 border border-green-300 rounded text-green-700 mb-4"
                        >
                            Download completed successfully.
                        </div>

                        @if($download->file_path)

                            <a
                                href="{{ route('downloads.file', $download) }}"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded"
                            >
                                Download File
                            </a>

                        @endif

                    @endif

                </div>

                <div class="mt-8">

                    <a
                        href="{{ route('dashboard') }}"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded"
                    >
                        Back to Dashboard
                    </a>

                </div>

            </div>

        </div>

    </div>

@if(
    $download->status === 'pending' ||
    $download->status === 'processing'
)

<script>

const downloadId = {{ $download->id }};

function pollStatus()
{
    fetch(`/downloads/${downloadId}/status`)

        .then(response => response.json())

        .then(data => {

            if (data.title) {

                document.getElementById(
                    'videoTitle'
                ).innerText = data.title;
            }

            const statusElement =
                document.getElementById(
                    'statusText'
                );

            statusElement.innerText =
                data.status;

            statusElement.className =
                'font-bold';

            if (
                data.status === 'completed'
            ) {

                statusElement.classList.add(
                    'text-green-600'
                );

            } else if (
                data.status === 'failed'
            ) {

                statusElement.classList.add(
                    'text-red-600'
                );

            } else if (
                data.status === 'processing'
            ) {

                statusElement.classList.add(
                    'text-blue-600'
                );
            }

            document.getElementById(
                'progressText'
            ).innerText =
                Number(
                    data.progress_percent ?? 0
                ).toFixed(2) + ' %';

            const bar =
                document.getElementById(
                    'progressBar'
                );

            bar.style.width =
                (data.progress_percent ?? 0) + '%';

            bar.innerText =
                Math.round(
                    data.progress_percent ?? 0
                ) + '%';

            document.getElementById(
                'speedText'
            ).innerText =
                data.download_speed ?? 'N/A';

            if (data.eta_seconds) {

                let sec =
                    parseInt(
                        data.eta_seconds
                    );

                let h =
                    Math.floor(
                        sec / 3600
                    );

                let m =
                    Math.floor(
                        (sec % 3600) / 60
                    );

                let s =
                    sec % 60;

                document.getElementById(
                    'etaText'
                ).innerText =
                    String(h).padStart(2, '0')
                    + ':'
                    + String(m).padStart(2, '0')
                    + ':'
                    + String(s).padStart(2, '0');

            } else {

                document.getElementById(
                    'etaText'
                ).innerText = 'N/A';
            }

            if (
                data.file_size
            ) {

                document.getElementById(
                    'fileSizeText'
                ).innerText =
                    (
                        data.file_size /
                        1024 /
                        1024
                    ).toFixed(2)
                    + ' MB';
            }

            if (
                data.error_message
            ) {

                const box =
                    document.getElementById(
                        'errorContainer'
                    );

                box.classList.remove(
                    'hidden'
                );

                box.innerText =
                    data.error_message;
            }

            if (
                data.status === 'completed'
            ) {

                document.getElementById(
                    'downloadArea'
                ).innerHTML = `
                    <div
                        class="p-4 bg-green-100 border border-green-300 rounded text-green-700 mb-4"
                    >
                        Download completed successfully.
                    </div>

                    <a
                        href="/downloads/${downloadId}/file"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded"
                    >
                        Download File
                    </a>
                `;
            }

            if (
                data.status === 'completed' ||
                data.status === 'failed'
            ) {

                clearInterval(
                    pollingTimer
                );
            }

        })

        .catch(error => {

            console.error(
                error
            );

        });
}

pollStatus();

const pollingTimer =
    setInterval(
        pollStatus,
        2000
    );

</script>

@endif

</x-app-layout>