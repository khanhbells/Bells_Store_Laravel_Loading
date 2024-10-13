<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;


class Slide extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;
    protected $fillable = [
        'name',
        'description',
        'keyword',
        'image',
        'icon',
        'album',
        'publish',
        'order',
        'user_id',
    ];
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'Slide_language', 'Slide_id', 'language_id')->withPivot('Slide_id', 'language_id', 'name', 'canonical')->withTimestamps();
    }
}
