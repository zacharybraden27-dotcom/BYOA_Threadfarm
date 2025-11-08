<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tweet extends Model
{
    protected $fillable = [
        'blog_post_id',
        'content',
        'status',
        'character_count',
        'posted_at',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
    ];

    public function blogPost(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($tweet) {
            $tweet->character_count = mb_strlen($tweet->content);
        });
    }
}
