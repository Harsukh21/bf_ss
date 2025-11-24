@extends('layouts.public')

@section('title', 'Upload Script')

@section('content')
<div class="min-h-screen bg-gray-50 py-10">
    <div class="max-w-xl mx-auto bg-white shadow-lg rounded-2xl p-8 border border-gray-100">
        @if(session('summary'))
            @php($summary = session('summary'))
            <div class="mb-6 rounded-xl border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-800">
                <div class="font-semibold text-green-900">Fuck Off</div>
                <div class="mt-1 flex gap-4">
                    <span>Updated: <strong>{{ $summary['updated'] ?? 0 }}</strong></span>
                    <span>Not updated: <strong>{{ $summary['not_updated'] ?? 0 }}</strong></span>
                </div>
            </div>
        @endif

        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-purple-600 text-white flex items-center justify-center font-semibold">
                UP
            </div>
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Upload Script</h1>
                <p class="text-sm text-gray-500">Select a file and run your script safely.</p>
            </div>
        </div>

        <form method="post" action="{{ route('runscript') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <label class="block">
                <span class="text-sm font-medium text-gray-700">Choose file</span>
                <input type="file" name="file" required
                       class="mt-2 block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </label>

            <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-primary-600 to-purple-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:from-primary-700 hover:to-purple-700 focus:ring-2 focus:ring-primary-500 focus:outline-none transition-all">
                Run Script
            </button>
        </form>
    </div>
</div>
@endsection
