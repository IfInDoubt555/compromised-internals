<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RallyEventDay extends Model
{
    protected $fillable = ['rally_event_id','date','label'];
    protected $casts = ['date' => 'date'];

    public function event()  { return $this->belongsTo(RallyEvent::class, 'rally_event_id'); }
    public function stages() { return $this->hasMany(RallyStage::class, 'rally_event_day_id')->orderBy('ss_number'); }
}
