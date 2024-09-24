<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'image',
        'album',
        'publish',
        'order',
        'user_id',
        'follow'
    ];
    protected $table = 'posts';
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'post_language', 'language_id', 'post_id')->withPivot('name', 'canonical', 'meta_title', 'meta_keyword', 'meta_description', 'description', 'content')->withTimestamps();
    }
    public function post_catalogues()
    {
        return $this->belongsToMany(PostCatalogue::class, 'post_catalogue_post', 'post_catalogue_id', 'post_id');
    }
}
