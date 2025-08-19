<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        ];
    }

    public function event() { return $this->belongsTo(RallyEvent::class, 'rally_event_id'); }
    public function day()   { return $this->belongsTo(RallyEventDay::class, 'rally_event_day_id'); }
}
