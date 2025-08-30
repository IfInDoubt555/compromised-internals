<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    protected function casts(): array
    {
        return [
            'is_super_special'       => 'boolean',
            'start_time_local'       => 'datetime',
            'second_pass_time_local' => 'datetime',
            'spectator_zones'        => 'array',
            'distance_km'            => 'decimal:2',
        ];
    }

    // Consistent 2-decimal presentation for distance.
    //  Usage: $stage->distance_km_formatted  // "4.90"
    public function getDistanceKmFormattedAttribute(): ?string
    {
        if ($this->distance_km === null) return null;
        return number_format((float) $this->distance_km, 2, '.', '');
    }

    public function event()
    {
        return $this->belongsTo(RallyEvent::class, 'rally_event_id');
    }

    public function day()
    {
        return $this->belongsTo(RallyEventDay::class, 'rally_event_day_id');
    }

    /**
     * Convenience accessor if you want a guaranteed absolute URL in views:
     * {{ $stage->map_image_src }}
     */
    public function getMapImageSrcAttribute(): ?string
    {
        $p = $this->map_image_url;
        if (!$p) return null;
        if (Str::startsWith($p, ['http://', 'https://', '//'])) {
            return $p;
        }
        return asset(ltrim($p, '/'));
    }
}