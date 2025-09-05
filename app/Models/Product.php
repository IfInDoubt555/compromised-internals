<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * @use HasFactory<\Database\Factories\ProductFactory>
 */
class Product extends Model
{
    use HasFactory;
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
