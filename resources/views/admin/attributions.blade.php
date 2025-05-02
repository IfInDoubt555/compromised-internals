@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold mb-6">🖼️ Attribution Manager</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.attributions.bulkUpdate') }}" method="POST">
        @csrf
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-sm border border-gray-300 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="px-3 py-2">Image</th>
                        <th class="px-3 py-2">Filename</th>
                        <th class="px-3 py-2">Year</th>
                        <th class="px-3 py-2">Section</th>
                        <th class="px-3 py-2">Author</th>
                        <th class="px-3 py-2">Source URL</th>
                        <th class="px-3 py-2">License</th>
                        <th class="px-3 py-2">Credit Preview</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $index => $entry)
                        <tr class="border-t bg-white hover:bg-gray-50 transition">
                            <td class="px-3 py-2">
                                <img src="{{ asset($entry['Path']) }}" class="w-24 rounded shadow">
                            </td>
                            <td class="px-3 py-2">{{ $entry['Filename'] }}</td>
                            <td class="px-3 py-2">{{ $entry['Year (Guess)'] }}</td>
                            <td class="px-3 py-2">{{ $entry['Section (Guess)'] }}</td>

                            {{-- Author --}}
                            <td class="px-3 py-2">
                                <input type="text"
                                    name="attributions[{{ $index }}][Author]"
                                    value="{{ $entry['Author'] }}"
                                    placeholder="Enter author"
                                    class="w-full border border-blue-400 bg-white/80 px-2 py-1 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            </td>

                            {{-- Source URL --}}
                            <td class="px-3 py-2">
                                <input type="text"
                                    name="attributions[{{ $index }}][Source URL]"
                                    value="{{ $entry['Source URL'] }}"
                                    placeholder="Enter source URL"
                                    class="w-full border border-blue-400 bg-white/80 px-2 py-1 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            </td>

                            {{-- License Type --}}
                            <td class="px-3 py-2">
                                <input type="text"
                                    name="attributions[{{ $index }}][License Type]"
                                    value="{{ $entry['License Type'] }}"
                                    placeholder="Enter license"
                                    class="w-full border border-blue-400 bg-white/80 px-2 py-1 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            </td>

                            {{-- Preview and hidden fields --}}
                            <td class="px-3 py-2 text-xs text-gray-700">
                                Photo by {{ $entry['Author'] ?: '...' }} via {{ $entry['Source URL'] ?: '...' }} – {{ $entry['License Type'] ?: '...' }}
                                <input type="hidden" name="attributions[{{ $index }}][Filename]" value="{{ $entry['Filename'] }}">
                                <input type="hidden" name="attributions[{{ $index }}][Path]" value="{{ $entry['Path'] }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-start">
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded shadow transition">
                💾 Save Updates
            </button>
        </div>
    </form>
</div>
@endsection