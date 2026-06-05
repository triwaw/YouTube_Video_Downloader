<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Download Status
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg p-6">

                <h2 class="text-xl font-bold mb-4">
                    Download Monitor
                </h2>

                <div class="space-y-3">

                    <div>
                        <strong>Status:</strong>
                        {{ $download->status }}
                    </div>

                    <div>
                        <strong>Progress:</strong>
                        {{ $download->progress_percent }} %
                    </div>

                    <div>
                        <strong>Speed:</strong>
                        {{ $download->download_speed ?? 'N/A' }}
                    </div>

                    <div>
                        <strong>ETA:</strong>

                        @if($download->eta_seconds)
                            {{ gmdate('i:s', $download->eta_seconds) }}
                        @else
                            N/A
                        @endif
                    </div>

                </div>
				
				@if($download->error_message)

						<div class="mt-6 p-4 bg-red-100 border border-red-400 rounded">

							<strong>Error:</strong>

							<pre class="mt-2 text-sm whitespace-pre-wrap">
					{{ $download->error_message }}
							</pre>

						</div>

					@endif

            </div>

        </div>

    </div>

</x-app-layout>