<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RallyEvent extends Model
{
    protected $fillable = [
        'name','location','description','start_date','end_date','slug','user_id','championship',
        'map_embed_url','official_url',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',   // ⬅ change to date
            'end_date'   => 'date',   // ⬅ change to date
        ];
    }

    public function days()   { return $this->hasMany(RallyEventDay::class)->orderBy('date'); }

    public function stages() { return $this->hasMany(RallyStage::class)->orderBy('ss_number'); }

    // app/Models/RallyEvent.php
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}