<?php

// app/Models/TravelHighlight.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelHighlight extends Model
{
    public const KIND_HIGHLIGHT = 'highlight';
    public const KIND_TIPS      = 'tips';

    protected $fillable = [
        'event_id',
        'title',
        'url',
        'sort_order',
        'is_active',
        'kind',
        'tips_md',
        'tips_selection', // ⬅ new
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'tips_selection' => 'array',   // ⬅ new
    ];

    /* Scopes */
    public function scopeHighlights($q) { return $q->where('kind', self::KIND_HIGHLIGHT); }
    public function scopeTips($q)       { return $q->where('kind', self::KIND_TIPS); }

    /**
     * Return only the selected tips (or all if none explicitly selected).
     */
    public function enabledTips(): array
    {
        $lines = collect(preg_split("/\r\n|\n|\r/", (string) $this->tips_md))
            ->map(fn ($l) => trim($l))
            ->filter()
            ->values();

        $sel = collect($this->tips_selection);

        // Default behavior: show all lines until the admin chooses specific ones
        if ($sel->isEmpty()) {
            return $lines->all();
        }

        return $sel
            ->map(fn ($i) => $lines->get((int) $i))
            ->filter()
            ->values()
            ->all();
    }
}