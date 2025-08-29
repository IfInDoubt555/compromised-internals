<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Thread extends Model
{
    protected $fillable = ['board_id','user_id','title','slug','body','last_activity_at'];

    protected $casts = ['last_activity_at' => 'datetime'];

    public function board(): BelongsTo { return $this->belongsTo(Board::class); }
    public function user(): BelongsTo  { return $this->belongsTo(User::class); }
    public function replies(): HasMany  { return $this->hasMany(Reply::class); }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class); // change table name if your pivot differs
    }

    // Bind by slug (so Thread $thread resolves from /threads/{slug})
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