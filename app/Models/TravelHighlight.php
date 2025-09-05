<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @use HasFactory<\Database\Factories\TravelHighlightFactory>
 */
class TravelHighlight extends Model
{
    use HasFactory;
    public const KIND_HIGHLIGHT = 'highlight';
    public const KIND_TIPS      = 'tips';

    /** @var list<string> */
    protected $fillable = [
        'event_id',
        'title',
        'url',
        'sort_order',
        'is_active',
        'kind',
        'tips_md',
        'tips_selection',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'is_active'      => 'boolean',
        'tips_selection' => 'array',
    ];

    /**
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeHighlights(Builder $query): Builder
    {
        return $query->where('kind', self::KIND_HIGHLIGHT);
    }

    /**
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeTips(Builder $query): Builder
    {
        return $query->where('kind', self::KIND_TIPS);
    }

    /**
     * Return only the selected tips (or all if none explicitly selected).
     *
     * @return list<string>
     */
    public function enabledTips(): array
    {
        $lines = collect(preg_split("/\r\n|\n|\r/", (string) ($this->tips_md ?? '')))
            ->map(static fn (string $l): string => trim($l))
            ->filter()
            ->values();

        $sel = collect($this->tips_selection ?? []);

        // Default: show all lines until specific ones are chosen
        if ($sel->isEmpty()) {
            /** @var list<string> */
            return $lines->all();
        }

        /** @var list<string> */
        return $sel
            ->map(static fn ($i) => $lines->get((int) $i))
            ->filter()
            ->values()
            ->all();
    }
}