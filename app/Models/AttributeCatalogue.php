<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class AttributeCatalogue extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;
    protected $fillable = [
        'parent_id',
        'lft',
        'rgt',
        'level',
        'image',
        'icon',
        'album',
        'publish',
        'order',
        'user_id',
        'follow'
    ];
    protected $table = 'attribute_catalogues';
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'attribute_catalogue_language', 'attribute_catalogue_id', 'language_id')->withPivot('name', 'canonical', 'meta_title', 'meta_keyword', 'meta_description', 'description', 'content')->withTimestamps();
    }
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_catalogue_attribute', 'attribute_catalogue_id', 'attribute_id');
    }
    public function attribute_catalogue_language()
    {
        return $this->hasMany(AttributeCatalogueLanguage::class, 'attribute_catalogue_id', 'id');
    }
    public static function isNodeCheck($id = 0)
    {
        $attributeCatalogue = AttributeCatalogue::find($id);
        // dd($attributeCatalogue);
        if ($attributeCatalogue->rgt - $attributeCatalogue->lft !== 1) {
            return false;
        }
        return true;
    }
    public function scopeKeyword($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', '%' . $keyword . '%');
        }
        return $query;
    }
}
