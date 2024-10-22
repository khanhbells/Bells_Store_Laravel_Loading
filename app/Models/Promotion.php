<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Promotion extends Model
{
    use HasFactory, QueryScopes, SoftDeletes;
    protected $guarded = [];
    protected $casts = [
        'discountInformation' => 'json',
    ];
    protected $table = 'promotions';

    public function products()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_product_variant', 'promotion_id', 'product_id')->withPivot(
            'product_variant_id',
            'model',
        )->withTimestamps();
    }
}
