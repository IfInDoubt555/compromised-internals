<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory; // This is sufficient to connect the factory

    /** @var list<string> */
    protected $fillable = [
        'order_id',
        'product_name',
        'quantity',
        'price',
        'size',
        'color',
    ];

    /** 
     * @return BelongsTo<Order, OrderItem>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}