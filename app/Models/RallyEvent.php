<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $location
 * @property string|null                     $description
 * @property string                          $slug
 * @property int|null                        $user_id
 * @property string|null                     $championship
 * @property string|null                     $map_embed_url
 * @property string|null                     $official_url
 * @property \Carbon\CarbonImmutable|null    $start_date
 * @property \Carbon\CarbonImmutable|null    $end_date
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int,\App\Models\RallyEventDay> $days
 * @property-read \Illuminate\Database\Eloquent\Collection<int,\App\Models\RallyStage>    $stages
 * 
 * @mixin \Eloquent
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

    /** @return HasMany<RallyEventDay> */
    public function days(): HasMany
    {
        return $this->hasMany(RallyEventDay::class)->orderBy('date');
    }

    /** @return HasMany<RallyStage> */
    public function stages(): HasMany
    {
        return $this->hasMany(RallyStage::class)->orderBy('ss_number');
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