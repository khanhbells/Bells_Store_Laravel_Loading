<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryScopes;

class Promotion extends Model
{
    use HasFactory, QueryScopes;
    protected $guarded = [];
}
