<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stripe_transaction_id',
        'total_amount',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
