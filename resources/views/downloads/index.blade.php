<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            UDownloader Dashboard
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <h3 class="text-2xl font-bold mb-4">
                    Hi {{ auth()->user()->name }}
                </h3>

                <p class="mb-6 text-gray-600">
                    Please provide YouTube URL to download
                </p>

                {{-- Analyze Form --}}
                <form method="POST" action="{{ route('download.analyze') }}">

                    @csrf

                    <div class="mb-4">

                        <input
                            type="url"
                            name="youtube_url"
                            required
                            placeholder="https://www.youtube.com/watch?v=..."
                            class="w-full border rounded-lg p-3"
                        >

                    </div>

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg"
                    >
                        Analyze Video
                    </button>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>