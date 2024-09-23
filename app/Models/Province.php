<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $table = 'provinces';
    protected $primaryKey = 'code';
    public $incrementing = false;
    // Mối quan hệ giữa provinces và districts là mối quan hệ 1...n
    public function districts()
    {
        return $this->hasMany(District::class, 'province_code', 'code');
    }
}
