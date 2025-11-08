<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogPost extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tweets(): HasMany
    {
        return $this->hasMany(Tweet::class);
    }

    public function getUnusedTweetsCountAttribute(): int
    {
        return $this->tweets()->where('status', 'draft')->count();
    }
}
