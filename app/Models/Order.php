<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;


class Order extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;
    protected $guarded = [];
    protected $casts = [
        'cart' => 'json',
        'promotion' => 'json'
    ];
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product', 'order_id', 'product_id')->withPivot('uuid', 'name', 'qty', 'price', 'priceOriginal', 'option');
    }
    public function order_payments()
    {
        return $this->hasMany(OrderPayment::class, 'order_id', 'id');
    }
}
