<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class Thread extends Model
{
    protected $fillable = [
        'board_id',
        'user_id',
        'title',
        'slug',
        'body',
        'last_activity_at',
        // scheduling
        'status',          // draft | scheduled | published
        'scheduled_for',   // datetime (UTC)
        'published_at',    // datetime (UTC)
    ];

    protected function casts(): array
    {
        return [
            'last_activity_at' => 'datetime',
            'scheduled_for'    => 'datetime',
            'published_at'     => 'datetime',
        ];
    }

    /** ---------- Scopes ---------- */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')
                 ->whereNotNull('published_at')
                 ->where('published_at', '<=', now());
    }

    public function scopeScheduled(Builder $q): Builder
    {
        return $q->where('status', 'scheduled')
                 ->whereNotNull('published_at')
                 ->where('published_at', '>', now());
    }

    public function scopeDrafts(Builder $q): Builder
    {
        return $q->where('status', 'draft');
    }

    /** ---------- Status helpers ---------- */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled' && $this->published_at && $this->published_at->isFuture();
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at && $this->published_at->isPast();
    }

    /** ---------- Relations ---------- */
    public function board(): BelongsTo   { return $this->belongsTo(Board::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function replies(): HasMany   { return $this->hasMany(Reply::class); }

    public function tags(): BelongsToMany
    {
        // ensure your pivot table is correct (likely 'thread_tag')
        return $this->belongsToMany(Tag::class, 'thread_tag')->withTimestamps();
    }

    /** ---------- Routing ---------- */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** ---------- Boot ---------- */
    protected static function booted(): void
    {
        static::saving(function (Thread $t) {
            if (empty($t->slug) && !empty($t->title)) {
                $t->slug = Str::slug($t->title);
            }
        });
    }
}