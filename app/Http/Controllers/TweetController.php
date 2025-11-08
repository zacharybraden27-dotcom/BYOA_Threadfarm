<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TweetController extends Controller
{
    public function update(Request $request, Tweet $tweet)
    {
        $tweet->load('blogPost');
        if ($tweet->blogPost->user_id !== Auth::id()) {
            abort(403);
        }

        $maxChars = config('threadfarm.tweet.max_character_count', 280);
        $request->validate([
            'content' => ['required', 'string', 'max:' . $maxChars],
        ]);

        $tweet->update([
            'content' => $request->content,
        ]);

        return back()->with('success', 'Tweet updated successfully!');
    }

    public function markAsPosted(Tweet $tweet)
    {
        $tweet->load('blogPost');
        if ($tweet->blogPost->user_id !== Auth::id()) {
            abort(403);
        }

        $statuses = config('threadfarm.tweet.statuses', ['draft', 'posted', 'discarded']);
        $tweet->update([
            'status' => $statuses[1], // 'posted'
            'posted_at' => now(),
        ]);

        return back()->with('success', 'Tweet marked as posted!');
    }

    public function discard(Tweet $tweet)
    {
        $tweet->load('blogPost');
        if ($tweet->blogPost->user_id !== Auth::id()) {
            abort(403);
        }

        $statuses = config('threadfarm.tweet.statuses', ['draft', 'posted', 'discarded']);
        $tweet->update([
            'status' => $statuses[2], // 'discarded'
        ]);

        return back()->with('success', 'Tweet discarded!');
    }

    public function restore(Tweet $tweet)
    {
        $tweet->load('blogPost');
        if ($tweet->blogPost->user_id !== Auth::id()) {
            abort(403);
        }

        $statuses = config('threadfarm.tweet.statuses', ['draft', 'posted', 'discarded']);
        $tweet->update([
            'status' => $statuses[0], // 'draft'
            'posted_at' => null,
        ]);

        return back()->with('success', 'Tweet restored to drafts!');
    }
}
