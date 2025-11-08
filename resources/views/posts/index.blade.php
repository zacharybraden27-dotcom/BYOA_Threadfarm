@extends('layouts.app')

@section('title', 'My Blog Posts')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Blog Posts</h1>
    <div class="flex gap-2">
        <a href="{{ route('posts.archived') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
            View Archived
        </a>
        <a href="{{ route('posts.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            Create New Post
        </a>
    </div>
</div>

<div class="mb-6">
    <form method="GET" action="{{ route('posts.index') }}" class="flex gap-2">
        <input type="text" 
               name="search" 
               value="{{ request('search') }}" 
               placeholder="Search posts..."
               class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
            Search
        </button>
        @if(request('search'))
            <a href="{{ route('posts.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded-md hover:bg-gray-500">
                Clear
            </a>
        @endif
    </form>
</div>

@if($posts->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 hover:shadow-lg transition-shadow">
                <h2 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">
                    <a href="{{ route('posts.show', $post) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                        {{ $post->title }}
                    </a>
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">
                    {{ Str::limit($post->content, 150) }}
                </p>
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                    <span>
                        <strong>{{ $post->unused_tweets_count }}</strong> unused tweets
                    </span>
                    <span>{{ $post->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('posts.show', $post) }}" class="flex-1 text-center bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">
                        View
                    </a>
                    <form method="POST" action="{{ route('posts.archive', $post) }}" class="inline" onsubmit="return confirm('Are you sure you want to archive this post?');">
                        @csrf
                        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 text-sm">
                            Archive
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
@else
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-12 text-center">
        <p class="text-gray-600 dark:text-gray-400 text-lg mb-4">No blog posts found.</p>
        <a href="{{ route('posts.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 inline-block">
            Create Your First Post
        </a>
    </div>
@endif
@endsection

