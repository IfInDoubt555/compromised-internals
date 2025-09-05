<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string|null $name
 * @property string|null $slug
 * @property string|null $location
 * @property string|null $city
 * @property string|null $country
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $date
 * @property-read \Illuminate\Database\Eloquent\Collection<int,\App\Models\RallyStage> $stages
 */
class RallyEventDay extends Model
{
    /** @var list<string> */
    protected $fillable = ['rally_event_id', 'date', 'label'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        // mutable Carbon via 'date'
        return ['date' => 'date'];
    }

    /** @return BelongsTo<RallyEvent, RallyEventDay> */
    public function event(): BelongsTo
    {
        return $this->belongsTo(RallyEvent::class, 'rally_event_id');
    }

    /** @return HasMany<RallyStage> */
    public function stages(): HasMany
    {
        return $this->hasMany(RallyStage::class, 'rally_event_day_id')
            ->orderBy('ss_number');
    }
}