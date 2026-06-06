<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            UDownloader Dashboard
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                <h1 class="text-2xl font-bold mb-6">
                    Welcome {{ auth()->user()->name }}
                </h1>

                <form
                    method="POST"
                    action="{{ route('download.analyze') }}"
                    class="mb-8"
                >
                    @csrf

                    <label class="block mb-2 font-semibold">
                        Please provide YouTube URL
                    </label>

                    <input
                        type="url"
                        name="youtube_url"
                        required
                        class="w-full border rounded p-3"
                        placeholder="https://www.youtube.com/watch?v=..."
                    >

                    @error('youtube_url')
                        <div class="text-red-600 mt-2">
                            {{ $message }}
                        </div>
                    @enderror

                    <button
                        type="submit"
                        class="mt-4 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded"
                    >
                        Analyze Video
                    </button>

                </form>

            </div>

            @if(isset($downloads) && $downloads->count())

                <div class="bg-white shadow rounded-lg p-6 mt-6">

                    <h2 class="text-xl font-bold mb-4">
                        Recent Downloads
                    </h2>

                    <table class="w-full border">

                        <thead>

                            <tr class="bg-gray-100">

                                <th class="border p-2 text-left">
                                    ID
                                </th>

                                <th class="border p-2 text-left">
                                    Title
                                </th>

                                <th class="border p-2 text-left">
                                    Status
                                </th>

                                <th class="border p-2 text-left">
                                    Created
                                </th>

                                <th class="border p-2 text-left">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($downloads as $download)

                                <tr>

                                    <td class="border p-2">
                                        {{ $download->id }}
                                    </td>

                                    <td class="border p-2">
                                        {{ $download->title ?? 'Pending...' }}
                                    </td>

                                    <td class="border p-2">

                                        @if($download->status === 'completed')

                                            <span class="text-green-600 font-bold">
                                                Completed
                                            </span>

                                        @elseif($download->status === 'failed')

                                            <span class="text-red-600 font-bold">
                                                Failed
                                            </span>

                                        @elseif($download->status === 'processing')

                                            <span class="text-blue-600 font-bold">
                                                Processing
                                            </span>

                                        @else

                                            <span class="text-gray-600">
                                                Pending
                                            </span>

                                        @endif

                                    </td>

                                    <td class="border p-2">
                                        {{ $download->created_at }}
                                    </td>

                                    <td class="border p-2">

                                        <a
                                            href="{{ route('downloads.show', $download) }}"
                                            class="text-blue-600"
                                        >
                                            Monitor
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                    <div class="mt-4">

                        {{ $downloads->links() }}

                    </div>

                </div>

            @endif

        </div>

    </div>

</x-app-layout>