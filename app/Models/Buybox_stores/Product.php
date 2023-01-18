<?php

namespace App\Models\Buybox_stores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $connection = 'buybox_stores';

    protected $fillable = [
        'store_id',
        'asin',
        'product_sku',
        'cyclic',
        'bb_cyclic',
        'priority',
        'availability',
        'latency',
        'base_price',
        'ceil_price',
        'app_360_price',
        'bb_price',
        'push_price',
        'store_price',
        'cyclic_push',
        'lowest_seller_id',
        'lowest_seller_price',
        'highest_seller_id',
        'highest_seller_price',
        'bb_winner_id',
        'bb_winner_price',
        'is_bb_won',
    ];
}
