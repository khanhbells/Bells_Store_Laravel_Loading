<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Product extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;
    protected $fillable = [
        'image',
        'album',
        'publish',
        'order',
        'user_id',
        'follow',
        'product_catalogue_id',
        'price',
        'made_in',
        'code',
    ];
    protected $table = 'products';
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'product_language', 'product_id', 'language_id')->withPivot('name', 'canonical', 'meta_title', 'meta_keyword', 'meta_description', 'description', 'content')->withTimestamps();
    }
    public function product_catalogues()
    {
        return $this->belongsToMany(ProductCatalogue::class, 'product_catalogue_product', 'product_id', 'product_catalogue_id');
    }
}
