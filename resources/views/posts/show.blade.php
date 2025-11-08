@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2 tracking-tight">{{ $post->title }}</h1>
            <p class="text-sm text-gray-400 font-medium">Created {{ $post->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('posts.edit', $post) }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-500 transition-colors font-semibold shadow-lg">
                Edit Post
            </a>
            <form method="POST" action="{{ route('posts.archive', $post) }}" class="inline" onsubmit="return confirm('Are you sure you want to archive this post?');">
                @csrf
                <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors font-semibold">
                    Archive
                </button>
            </form>
            <form method="POST" action="{{ route('posts.destroy', $post) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this post? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-500 transition-colors font-semibold">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="mb-6">
        <form method="POST" action="{{ route('posts.generate-tweets', $post) }}" class="inline">
            @csrf
            <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-500 transition-colors font-semibold shadow-lg">
                Generate Tweets
            </button>
        </form>
        @if($draftTweets->count() > 0)
            <form method="POST" action="{{ route('posts.generate-tweets', $post) }}" class="inline ml-3">
                @csrf
                <button type="submit" class="bg-yellow-600 text-white px-6 py-3 rounded-md hover:bg-yellow-500 transition-colors font-semibold shadow-lg" onclick="return confirm('This will regenerate all draft tweets. Continue?');">
                    Regenerate Tweets
                </button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Draft Tweets -->
        <div class="lg:col-span-2">
            <h2 class="text-3xl font-bold mb-4 text-white tracking-tight">
                Draft Tweets ({{ $draftTweets->count() }})
            </h2>
            @if($draftTweets->count() > 0)
                <div class="space-y-4">
                    @foreach($draftTweets as $tweet)
                        <div class="bg-gray-800 shadow-xl rounded-lg p-5 border-l-4 border-green-500 border border-gray-700">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-sm text-gray-400 font-medium">
                                    {{ $tweet->character_count }}/{{ $maxCharacterCount }} characters
                                </span>
                                <div class="flex gap-3">
                                    <button onclick="copyToClipboard('{{ $tweet->id }}')" class="text-green-400 hover:text-green-300 text-sm font-semibold transition-colors">
                                        Copy
                                    </button>
                                    <button onclick="editTweet('{{ $tweet->id }}')" class="text-gray-400 hover:text-white text-sm font-semibold transition-colors">
                                        Edit
                                    </button>
                                </div>
                            </div>
                            <p id="tweet-content-{{ $tweet->id }}" class="text-white mb-3 whitespace-pre-wrap leading-relaxed">{!! nl2br(e($tweet->content)) !!}</p>
                            <div id="edit-form-{{ $tweet->id }}" class="hidden mb-3">
                                <form method="POST" action="{{ route('tweets.update', $tweet) }}">
                                    @csrf
                                    @method('PUT')
                                    <textarea name="content" rows="3" maxlength="{{ $maxCharacterCount }}" class="w-full px-4 py-2 border border-gray-700 bg-gray-900 text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 font-medium">{{ $tweet->content }}</textarea>
                                    <div class="mt-2 flex gap-2">
                                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-500 text-sm font-semibold transition-colors">
                                            Save
                                        </button>
                                        <button type="button" onclick="cancelEdit('{{ $tweet->id }}')" class="bg-gray-700 text-white px-4 py-2 rounded-md hover:bg-gray-600 text-sm font-semibold transition-colors">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="flex gap-2 mt-4">
                                <form method="POST" action="{{ route('tweets.mark-posted', $tweet) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-500 text-sm font-semibold transition-colors shadow-lg">
                                        Mark as Posted
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('tweets.discard', $tweet) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-500 text-sm font-semibold transition-colors">
                                        Discard
                                    </button>
                                </form>
                            </div>
                            <textarea id="tweet-text-{{ $tweet->id }}" class="hidden">{{ $tweet->content }}</textarea>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-800 rounded-lg p-8 text-center border border-gray-700">
                    <p class="text-gray-400 font-medium">No draft tweets. Click "Generate Tweets" to create tweets from this post.</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Posted Tweets -->
            <div>
                <h3 class="text-xl font-bold mb-3 text-white tracking-tight">
                    Posted ({{ $postedTweets->count() }})
                </h3>
                @if($postedTweets->count() > 0)
                    <div class="space-y-3">
                        @foreach($postedTweets as $tweet)
                            <div class="bg-green-900/30 border border-green-700 rounded-lg p-3">
                                <p class="text-sm text-white mb-2 leading-relaxed">{{ Str::limit($tweet->content, 100) }}</p>
                                <p class="text-xs text-gray-400 font-medium">Posted {{ $tweet->posted_at->format('M d, Y') }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 font-medium">No posted tweets yet.</p>
                @endif
            </div>

            <!-- Discarded Tweets -->
            <div>
                <h3 class="text-xl font-bold mb-3 text-white tracking-tight">
                    Discarded ({{ $discardedTweets->count() }})
                </h3>
                @if($discardedTweets->count() > 0)
                    <div class="space-y-3">
                        @foreach($discardedTweets as $tweet)
                            <div class="bg-red-900/30 border border-red-700 rounded-lg p-3">
                                <p class="text-sm text-white mb-2 leading-relaxed">{{ Str::limit($tweet->content, 100) }}</p>
                                <form method="POST" action="{{ route('tweets.restore', $tweet) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-green-400 hover:text-green-300 font-semibold transition-colors">
                                        Restore
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 font-medium">No discarded tweets.</p>
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

