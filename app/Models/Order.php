<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\OrderItem;

/**
 * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\OrderFactory>
 */
class Order extends Model
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'stripe_transaction_id',
        'total_amount',
        'status',
    ];

    /** 
     * @return HasMany<OrderItem, Order>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}