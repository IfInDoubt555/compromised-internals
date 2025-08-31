<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

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

    /** Scopes */
    public function scopePublished($q) { return $q->where('status', 'published'); }
    public function scopeScheduled($q) { return $q->where('status', 'scheduled'); }
    public function scopeDraft($q)     { return $q->where('status', 'draft'); }

    public function isPublished(): bool
    {
        return $this->status === 'published' && !is_null($this->published_at);
    }

    /** Relations */
    public function board(): BelongsTo   { return $this->belongsTo(Board::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function replies(): HasMany   { return $this->hasMany(Reply::class); }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class); // update pivot name if different
    }

    /** Route model binding by slug */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saving(function (Thread $t) {
            if (empty($t->slug) && !empty($t->title)) {
                $t->slug = Str::slug($t->title);
            }
        });
    }
}