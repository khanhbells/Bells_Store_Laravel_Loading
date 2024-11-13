<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryScopes;


class Review extends Model
{
    use HasFactory, QueryScopes;
    protected $guarded = [];

    public function reviewable()
    {
        return $this->morphTo();
    }
}
