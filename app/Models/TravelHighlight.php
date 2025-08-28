<?php

// app/Models/TravelHighlight.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelHighlight extends Model
{
    public const KIND_HIGHLIGHT = 'highlight';
    public const KIND_TIPS      = 'tips';

    protected $fillable = [
        'event_id','title','url','sort_order','is_active',
        'kind','tips_md',
    ];

    /* Scopes */
    public function scopeHighlights($q) { return $q->where('kind', self::KIND_HIGHLIGHT); }
    public function scopeTips($q)       { return $q->where('kind', self::KIND_TIPS); }
}