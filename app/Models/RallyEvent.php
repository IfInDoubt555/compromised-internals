<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\RallyEventFactory>
 *
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $location
 * @property string|null $city
 * @property string|null $country
 * @property string|null $description
 * @property string|null $championship
 * @property string|null $map_embed_url
 * @property string|null $official_url
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property-read \Illuminate\Database\Eloquent\Collection<int,\App\Models\RallyEventDay> $days
 */
class RallyEvent extends Model
{
    use HasFactory;

    /** @var list<string> */
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

    /** @return array<string,string> */
    protected function casts(): array
    {
        // Use mutable Carbon (Laravel default) to align with app-wide choice
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    /** @return HasMany<RallyEventDay> */
    public function days(): HasMany
    {
        return $this->hasMany(RallyEventDay::class, 'rally_event_id')
            ->orderBy('date');
    }

    /** @return HasMany<RallyStage> */
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