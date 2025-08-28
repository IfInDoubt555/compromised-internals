<?php

// app/Models/TravelHighlight.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelHighlight extends Model
{
    protected $fillable = ['event_id','title','url','sort_order','is_active'];
    protected $casts = ['is_active' => 'boolean'];
}