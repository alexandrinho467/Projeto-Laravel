<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id', 'product_size_id', 'product_name', 'product_brand',
        'size', 'type', 'qty', 'order_id', 'notes',
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function order()   { return $this->belongsTo(Order::class); }
}
