<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property-read RallyEvent|null      $event
 * @property-read RallyEventDay|null   $day
 * @property-read RallyEventDay|null   $secondDay
 *
 * @property int|null                  $rally_event_id
 * @property int|null                  $rally_event_day_id
 * @property string|null               $name
 * @property string|null               $ss_number
 * @property int|null                  $ss_number
 * @property string|null               $second_ss_number
 * @property float|string|null         $distance_km
 * @property \Illuminate\Support\Carbon|null $start_time_local
 * @property \Illuminate\Support\Carbon|null $second_pass_time_local
 * @property string|\Carbon\CarbonInterface|null $start_time_local
 * @property string|\Carbon\CarbonInterface|null $second_pass_time_local
 * @property string|null               $location
 * @property string|null               $map_image_url
 * @property string|null               $map_embed_url
 * @property bool                      $is_super_special
 * @property string|null               $stage_type
 *
 * @property-read string|null          $distance_km_formatted
 * @property-read string|null          $map_image_src
 */
class RallyStage extends Model
{
    protected $fillable = [
        'rally_event_id',
        'rally_event_day_id',
        'name',
        'ss_number',
        'distance_km',
        'start_time_local',
        'second_pass_time_local',
        'map_image_url',
        'map_embed_url',
        'is_super_special',
        'stage_type',
        'second_ss_number',
        'second_rally_event_day_id',
    ];
        /** @return array<string,string> */

    protected function casts(): array
    {
        return [
            'is_super_special'       => 'boolean',
            'start_time_local'       => 'datetime',
            'second_pass_time_local' => 'datetime',
            'spectator_zones'        => 'array',
            'distance_km'            => 'decimal:2', // returns string
        ];
    }

    public function getDistanceKmFormattedAttribute(): ?string
    {
        if ($this->distance_km === null) {
            return null;
        }
        return number_format((float) $this->distance_km, 2, '.', '');
    }

    /**
     * @return BelongsTo<RallyEvent, RallyStage>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(RallyEvent::class, 'rally_event_id');
    }

    /**
     * @return BelongsTo<RallyEventDay, RallyStage>
     */
    public function day(): BelongsTo
    {
        return $this->belongsTo(RallyEventDay::class, 'rally_event_day_id');
    }

    public function secondDay(): BelongsTo
    {
        return $this->belongsTo(RallyEventDay::class, 'second_rally_event_day_id');
    }

    public function getMapImageSrcAttribute(): ?string
    {
        $p = $this->map_image_url;
        if (!$p) {
            return null;
        }
        if (Str::startsWith($p, ['http://', 'https://', '//'])) {
            return $p;
        }
        return asset(ltrim($p, '/'));
    }
}