<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = ['name','slug','icon','color','position','is_public','description'];

    public function threads() {
        return $this->hasMany(Thread::class);
    }
}