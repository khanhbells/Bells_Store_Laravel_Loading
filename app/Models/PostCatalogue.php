<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCatalogue extends Model
{
    use HasFactory, SoftDeletes;
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
    protected $table = 'post_catalogues';
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'post_catalogue_language', 'post_catalogue_id', 'language_id')->withPivot('name', 'canonical', 'meta_title', 'meta_keyword', 'meta_description', 'description', 'content')->withTimestamps();
    }
    public function post_catalogue_language()
    {
        return $this->hasMany(PostCatalogueLanguage::class, 'post_catalogue_id', 'id');
    }
    public static function isNodeCheck($id = 0)
    {
        $postCatalogue = PostCatalogue::find($id);
        // dd($postCatalogue);
        if ($postCatalogue->rgt - $postCatalogue->lft !== 1) {
            return false;
        }
        return true;
    }
}
