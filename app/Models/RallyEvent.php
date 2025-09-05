<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, RallyEventDay> $days
 * @property-read \Illuminate\Database\Eloquent\Collection<int, RallyStage> $stages
 */

class RallyEvent extends Model
{
    use HasFactory;

    /** @var array<int,string> */
    protected $fillable = [
        'name',
        'location',
        'description',
        'start_date',
        'end_date',
        'slug',
        'user_id',
        'championship',
        'map_embed_url',
        'official_url',
    ];

    /**
     * Use immutable Carbon so calls like ->toDateString(), ->copy(), ->isFuture() are known-safe.
     *
     * @return array<string,string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'immutable_date',
            'end_date'   => 'immutable_date',
        ];
    }

    /** @return HasMany<RallyEventDay, RallyEvent> */
    public function days(): HasMany
    {
        return $this->hasMany(RallyEventDay::class, 'rally_event_id')
            ->orderBy('date');
    }

    /** @return HasMany<RallyStage, RallyEvent> */
    public function stages(): HasMany
    {
        return $this->hasMany(RallyStage::class, 'rally_event_id')
            ->orderBy('ss_number');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null): self
    {
        /** @var self $found */
        $found = $this->where('slug', $value)
            ->orWhere('id', $value)
            ->firstOrFail();

        return $found;
    }
}