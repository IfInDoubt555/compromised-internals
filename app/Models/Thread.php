<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thread extends Model
{
    protected $fillable = ['board_id','user_id','title','slug','body','last_activity_at'];

    protected $casts = ['last_activity_at' => 'datetime'];

    public function board(): BelongsTo { return $this->belongsTo(Board::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function replies() { return $this->hasMany(Reply::class); }
}