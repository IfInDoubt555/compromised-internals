<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateClick extends Model
{
    protected $fillable = [
        'brand','subid','url','host','user_id','ip','ua','referer',
    ];
}