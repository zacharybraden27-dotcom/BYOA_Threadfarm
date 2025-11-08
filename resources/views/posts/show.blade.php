@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $post->title }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Created {{ $post->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('posts.edit', $post) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Edit Post
            </a>
            <form method="POST" action="{{ route('posts.archive', $post) }}" class="inline" onsubmit="return confirm('Are you sure you want to archive this post?');">
                @csrf
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Archive
                </button>
            </form>
            <form method="POST" action="{{ route('posts.destroy', $post) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this post? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="mb-6">
        <form method="POST" action="{{ route('posts.generate-tweets', $post) }}" class="inline">
            @csrf
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700">
                Generate Tweets
            </button>
        </form>
        @if($draftTweets->count() > 0)
            <form method="POST" action="{{ route('posts.generate-tweets', $post) }}" class="inline ml-2">
                @csrf
                <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-md hover:bg-yellow-700" onclick="return confirm('This will regenerate all draft tweets. Continue?');">
                    Regenerate Tweets
                </button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Draft Tweets -->
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">
                Draft Tweets ({{ $draftTweets->count() }})
            </h2>
            @if($draftTweets->count() > 0)
                <div class="space-y-4">
                    @foreach($draftTweets as $tweet)
                        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 border-l-4 border-blue-500">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $tweet->character_count }}/280 characters
                                </span>
                                <div class="flex gap-2">
                                    <button onclick="copyToClipboard('{{ $tweet->id }}')" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm">
                                        Copy
                                    </button>
                                    <button onclick="editTweet('{{ $tweet->id }}')" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300 text-sm">
                                        Edit
                                    </button>
                                </div>
                            </div>
                            <p id="tweet-content-{{ $tweet->id }}" class="text-gray-900 dark:text-white mb-3 whitespace-pre-wrap">{!! nl2br(e($tweet->content)) !!}</p>
                            <div id="edit-form-{{ $tweet->id }}" class="hidden mb-3">
                                <form method="POST" action="{{ route('tweets.update', $tweet) }}">
                                    @csrf
                                    @method('PUT')
                                    <textarea name="content" rows="3" maxlength="280" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">{{ $tweet->content }}</textarea>
                                    <div class="mt-2 flex gap-2">
                                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">
                                            Save
                                        </button>
                                        <button type="button" onclick="cancelEdit('{{ $tweet->id }}')" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 text-sm">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('tweets.mark-posted', $tweet) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                                        Mark as Posted
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('tweets.discard', $tweet) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm">
                                        Discard
                                    </button>
                                </form>
                            </div>
                            <textarea id="tweet-text-{{ $tweet->id }}" class="hidden">{{ $tweet->content }}</textarea>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-8 text-center">
                    <p class="text-gray-600 dark:text-gray-400">No draft tweets. Click "Generate Tweets" to create tweets from this post.</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Posted Tweets -->
            <div>
                <h3 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">
                    Posted ({{ $postedTweets->count() }})
                </h3>
                @if($postedTweets->count() > 0)
                    <div class="space-y-3">
                        @foreach($postedTweets as $tweet)
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                                <p class="text-sm text-gray-900 dark:text-white mb-2">{{ Str::limit($tweet->content, 100) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Posted {{ $tweet->posted_at->format('M d, Y') }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No posted tweets yet.</p>
                @endif
            </div>

            <!-- Discarded Tweets -->
            <div>
                <h3 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">
                    Discarded ({{ $discardedTweets->count() }})
                </h3>
                @if($discardedTweets->count() > 0)
                    <div class="space-y-3">
                        @foreach($discardedTweets as $tweet)
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                                <p class="text-sm text-gray-900 dark:text-white mb-2">{{ Str::limit($tweet->content, 100) }}</p>
                                <form method="POST" action="{{ route('tweets.restore', $tweet) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                                        Restore
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No discarded tweets.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(tweetId) {
    const text = document.getElementById('tweet-text-' + tweetId).value;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Tweet copied to clipboard!');
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
            fallbackCopy(text);
        });
    } else {
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        alert('Tweet copied to clipboard!');
    } catch (err) {
        console.error('Fallback copy failed: ', err);
        alert('Failed to copy tweet. Please copy manually.');
    }
    document.body.removeChild(textArea);
}

function editTweet(tweetId) {
    document.getElementById('tweet-content-' + tweetId).classList.add('hidden');
    document.getElementById('edit-form-' + tweetId).classList.remove('hidden');
}

function cancelEdit(tweetId) {
    document.getElementById('tweet-content-' + tweetId).classList.remove('hidden');
    document.getElementById('edit-form-' + tweetId).classList.add('hidden');
}
</script>
@endsection

