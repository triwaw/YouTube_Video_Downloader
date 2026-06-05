<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Analyze Video
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg p-6">

                <h2 class="text-2xl font-bold mb-4">
                    {{ $video['title'] ?? 'Unknown Title' }}
                </h2>

                @if(!empty($video['thumbnail']))
                    <img
                        src="{{ $video['thumbnail'] }}"
                        class="w-64 rounded mb-4"
                    >
                @endif

                <p class="mb-4">
                    Duration:
                    {{ gmdate('H:i:s', $video['duration'] ?? 0) }}
                </p>

                <form method="POST" action="{{ route('download.store') }}">

                    @csrf

                    <input
                        type="hidden"
                        name="youtube_url"
                        value="{{ $youtube_url }}"
                    >

                    <div class="space-y-3">

                        @foreach($formats as $format)

                            <label class="block border p-3 rounded">

                                <input
                                    type="radio"
                                    name="format_id"
                                    value="{{ $format['format_id'] }}"
                                    required
                                >

                                <strong>
                                    
									
									{{ $format['label'] }}
                                </strong>

                                |


                            </label>

                        @endforeach

                    </div>

                    <button
                        type="submit"
                        class="mt-6 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg"
                    >
                        Start Download
                    </button>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>