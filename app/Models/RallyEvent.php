<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RallyEvent extends Model
{
    protected $fillable = [
        'name','location','description','start_date','end_date','slug','user_id','championship', 'map_embed_url',
        // optionally add: 'surface','timezone','website_url','tickets_url','hero_image_path'
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date'   => 'datetime',
        ];
    }

    // relationships
    public function days()   { return $this->hasMany(RallyEventDay::class)->orderBy('date'); }
    public function stages() { return $this->hasMany(RallyStage::class)->orderBy('ss_number'); }
}
