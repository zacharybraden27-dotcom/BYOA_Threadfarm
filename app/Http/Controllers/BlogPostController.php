<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::where('user_id', Auth::id())
            ->whereNull('archived_at');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $draftStatus = config('threadfarm.tweet.statuses', ['draft', 'posted', 'discarded'])[0];
        $posts = $query->withCount(['tweets as unused_tweets_count' => function ($query) use ($draftStatus) {
            $query->where('status', $draftStatus);
        }])->latest()->paginate(config('threadfarm.pagination.posts_per_page', 12));

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = BlogPost::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('posts.show', $post)->with('success', 'Blog post created successfully!');
    }

    public function show(BlogPost $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $post->load('tweets');
        $statuses = config('threadfarm.tweet.statuses', ['draft', 'posted', 'discarded']);
        $draftTweets = $post->tweets()->where('status', $statuses[0])->get();
        $postedTweets = $post->tweets()->where('status', $statuses[1])->get();
        $discardedTweets = $post->tweets()->where('status', $statuses[2])->get();
        $maxCharacterCount = config('threadfarm.tweet.max_character_count', 280);

        return view('posts.show', compact('post', 'draftTweets', 'postedTweets', 'discardedTweets', 'maxCharacterCount'));
    }

    public function edit(BlogPost $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, BlogPost $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->update($request->only(['title', 'content']));

        return redirect()->route('posts.show', $post)->with('success', 'Blog post updated successfully!');
    }

    public function destroy(BlogPost $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $post->delete();

        // Redirect to archived posts if we're coming from there
        if (request()->has('from_archived')) {
            return redirect()->route('posts.archived')->with('success', 'Blog post deleted successfully!');
        }

        return redirect()->route('posts.index')->with('success', 'Blog post deleted successfully!');
    }

    public function archive(BlogPost $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $post->update(['archived_at' => now()]);

        return redirect()->route('posts.index')->with('success', 'Blog post archived successfully!');
    }

    public function archived(Request $request)
    {
        $query = BlogPost::where('user_id', Auth::id())
            ->whereNotNull('archived_at');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $draftStatus = config('threadfarm.tweet.statuses', ['draft', 'posted', 'discarded'])[0];
        $posts = $query->withCount(['tweets as unused_tweets_count' => function ($query) use ($draftStatus) {
            $query->where('status', $draftStatus);
        }])->latest('archived_at')->paginate(config('threadfarm.pagination.posts_per_page', 12));

        return view('posts.archived', compact('posts'));
    }

    public function unarchive(BlogPost $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $post->update(['archived_at' => null]);

        return redirect()->route('posts.archived')->with('success', 'Blog post unarchived successfully!');
    }

    public function generateTweets(Request $request, BlogPost $post, GeminiService $geminiService)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $tweets = $geminiService->generateTweets($post->content, $post->title);

            // Delete existing draft tweets
            $statuses = config('threadfarm.tweet.statuses', ['draft', 'posted', 'discarded']);
            $draftStatus = $statuses[0];
            $post->tweets()->where('status', $draftStatus)->delete();

            // Create new tweets
            foreach ($tweets as $tweetContent) {
                if (!empty(trim($tweetContent))) {
                    $post->tweets()->create([
                        'content' => $tweetContent,
                        'status' => $draftStatus,
                    ]);
                }
            }

            return redirect()->route('posts.show', $post)->with('success', 'Tweets generated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('posts.show', $post)->with('error', 'Failed to generate tweets: ' . $e->getMessage());
        }
    }
}
