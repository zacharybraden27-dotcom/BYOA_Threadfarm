@extends('layouts.app')

@section('title', 'Archived Posts')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Archived Posts</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">View and manage your archived blog posts</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('posts.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
            Back to Posts
        </a>
    </div>
</div>

<div class="mb-6">
    <form method="GET" action="{{ route('posts.archived') }}" class="flex gap-2">
        <input type="text" 
               name="search" 
               value="{{ request('search') }}" 
               placeholder="Search archived posts..."
               class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
            Search
        </button>
        @if(request('search'))
            <a href="{{ route('posts.archived') }}" class="bg-gray-400 text-white px-4 py-2 rounded-md hover:bg-gray-500">
                Clear
            </a>
        @endif
    </form>
</div>

@if($posts->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 hover:shadow-lg transition-shadow border-l-4 border-yellow-500">
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
                    <span>Archived {{ $post->archived_at->format('M d, Y') }}</span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('posts.show', $post) }}" class="flex-1 text-center bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">
                        View
                    </a>
                    <form method="POST" action="{{ route('posts.unarchive', $post) }}" class="inline" onsubmit="return confirm('Are you sure you want to unarchive this post?');">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                            Unarchive
                        </button>
                    </form>
                    <form method="POST" action="{{ route('posts.destroy', $post) }}?from_archived=1" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this post? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm">
                            Delete
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
        <p class="text-gray-600 dark:text-gray-400 text-lg mb-4">No archived posts found.</p>
        <a href="{{ route('posts.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 inline-block">
            Back to Posts
        </a>
    </div>
@endif
@endsection

