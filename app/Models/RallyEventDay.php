<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property \Illuminate\Support\Carbon|null $date
 * @property-read RallyEvent $event
 * @property-read \Illuminate\Database\Eloquent\Collection<int, RallyStage> $stages
 */
class RallyEventDay extends Model
{
    /** @var list<string> */
    protected $fillable = ['rally_event_id', 'date', 'label'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    /**
     * @return BelongsTo<RallyEvent, RallyEventDay>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(RallyEvent::class, 'rally_event_id');
    }

    /**
     * @return HasMany<RallyStage, RallyEventDay>
     */
    public function stages(): HasMany
    {
        return $this->hasMany(RallyStage::class, 'rally_event_day_id')
            ->orderBy('ss_number');
    }
}