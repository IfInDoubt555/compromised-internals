<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RallyEvent extends Model
{
    protected $fillable = [
        'name',
        'location',
        'description',
        'start_date',
        'end_date',
        'slug',
        'user_id', // if applicable
    ];
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }
}
