@extends('layouts.app')

@section('title', 'Create Blog Post')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-4xl font-bold mb-6 text-white tracking-tight">Create New Blog Post</h1>

    <div class="bg-gray-800 shadow-xl rounded-lg p-8 border border-gray-700">
        <form method="POST" action="{{ route('posts.store') }}">
            @csrf

            <div class="mb-6">
                <label for="title" class="block text-sm font-semibold text-white mb-2">Title</label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="{{ old('title') }}" 
                       required
                       class="w-full px-4 py-3 border border-gray-700 bg-gray-900 text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 font-medium placeholder-gray-500">
                @error('title')
                    <p class="mt-2 text-sm text-red-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="content" class="block text-sm font-semibold text-white mb-2">Content</label>
                <textarea id="content" 
                          name="content" 
                          rows="15" 
                          required
                          class="w-full px-4 py-3 border border-gray-700 bg-gray-900 text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 font-medium placeholder-gray-500">{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-2 text-sm text-red-400 font-medium">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-400 font-medium">Paste your blog post content here</p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-500 transition-colors font-semibold shadow-lg">
                    Save Post
                </button>
                <a href="{{ route('posts.index') }}" class="bg-gray-700 text-white px-6 py-3 rounded-md hover:bg-gray-600 transition-colors font-semibold">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

