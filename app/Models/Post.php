<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // (optional import)

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'image_path',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Makes sure the view being used is the slug and not the id
    public function getRouteKeyName()
    {
        return 'slug';
    }

}