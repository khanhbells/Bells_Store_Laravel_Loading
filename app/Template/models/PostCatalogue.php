<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class {class}Catalogue extends Model
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
    protected $table = '{module}_catalogues';
    public function languages()
    {
        return $this->belongsToMany(Language::class, '{module}_catalogue_language', '{module}_catalogue_id', 'language_id')->withPivot('name', 'canonical', 'meta_title', 'meta_keyword', 'meta_description', 'description', 'content')->withTimestamps();
    }
    public function {module}s()
    {
        return $this->belongsToMany({class}::class, '{module}_catalogue_{module}', '{module}_catalogue_id', '{module}_id');
    }
    public function {module}_catalogue_language()
    {
        return $this->hasMany({class}CatalogueLanguage::class, '{module}_catalogue_id', 'id');
    }
    public static function isNodeCheck($id = 0)
    {
        ${module}Catalogue = {class}Catalogue::find($id);
        // dd(${module}Catalogue);
        if (${module}Catalogue->rgt - ${module}Catalogue->lft !== 1) {
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
