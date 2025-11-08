@extends('layouts.app')

@section('title', 'Archived Posts')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-4xl font-bold text-white tracking-tight">Archived Posts</h1>
        <p class="text-sm text-gray-400 mt-1 font-medium">View and manage your archived blog posts</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('posts.index') }}" class="bg-gray-700 text-white px-5 py-2.5 rounded-md hover:bg-gray-600 transition-colors font-semibold shadow-lg">
            Back to Posts
        </a>
    </div>
</div>

<div class="mb-6">
    <form method="GET" action="{{ route('posts.archived') }}" class="flex gap-3">
        <input type="text" 
               name="search" 
               value="{{ request('search') }}" 
               placeholder="Search archived posts..."
               class="flex-1 px-4 py-2.5 border border-gray-700 bg-gray-800 text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 placeholder-gray-500 font-medium">
        <button type="submit" class="bg-gray-700 text-white px-5 py-2.5 rounded-md hover:bg-gray-600 transition-colors font-semibold shadow-lg">
            Search
        </button>
        @if(request('search'))
            <a href="{{ route('posts.archived') }}" class="bg-gray-700 text-white px-5 py-2.5 rounded-md hover:bg-gray-600 transition-colors font-semibold">
                Clear
            </a>
        @endif
    </form>
</div>

@if($posts->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
            <div class="bg-gray-800 shadow-xl rounded-lg p-6 hover:shadow-2xl transition-all border-l-4 border-yellow-500 border border-gray-700">
                <h2 class="text-xl font-bold mb-3 text-white">
                    <a href="{{ route('posts.show', $post) }}" class="hover:text-green-400 transition-colors">
                        {{ $post->title }}
                    </a>
                </h2>
                <p class="text-gray-400 text-sm mb-4 line-clamp-3 leading-relaxed">
                    {{ Str::limit($post->content, 150) }}
                </p>
                <div class="flex items-center justify-between text-sm text-gray-400 mb-4 font-medium">
                    <span>
                        <strong class="text-green-400">{{ $post->unused_tweets_count }}</strong> unused tweets
                    </span>
                    <span>Archived {{ $post->archived_at->format('M d, Y') }}</span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('posts.show', $post) }}" class="flex-1 text-center bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-500 text-sm font-semibold transition-colors shadow-lg">
                        View
                    </a>
                    <form method="POST" action="{{ route('posts.unarchive', $post) }}" class="inline" onsubmit="return confirm('Are you sure you want to unarchive this post?');">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-500 text-sm font-semibold transition-colors">
                            Unarchive
                        </button>
                    </form>
                    <form method="POST" action="{{ route('posts.destroy', $post) }}?from_archived=1" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this post? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-500 text-sm font-semibold transition-colors">
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
    <div class="bg-gray-800 shadow-xl rounded-lg p-12 text-center border border-gray-700">
        <p class="text-gray-400 text-lg mb-4 font-medium">No archived posts found.</p>
        <a href="{{ route('posts.index') }}" class="bg-green-600 text-white px-5 py-2.5 rounded-md hover:bg-green-500 transition-colors inline-block font-semibold shadow-lg">
            Back to Posts
        </a>
    </div>
@endif
@endsection

