<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Thread extends Model
{
    protected $fillable = ['board_id','user_id','title','slug','body','last_activity_at'];

    protected $casts = ['last_activity_at' => 'datetime'];

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }

    public function tags(): BelongsToMany
    {
        // Default pivot name is singular snake_case in alpha order: tag_thread
        return $this->belongsToMany(Tag::class); 
        // If your pivot is named thread_tag instead, use:
        // return $this->belongsToMany(Tag::class, 'thread_tag');
    }
}