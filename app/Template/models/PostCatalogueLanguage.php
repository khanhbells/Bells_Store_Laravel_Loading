<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {class}CatalogueLanguage extends Model
{
    use HasFactory;
    protected $table = '{module}_catalogue_language';
    public function {module}_catalogue_language()
    {
        return $this->belongsTo({class}Catalogue::class, '{module}_catalogue_id', 'id');
    }
}
