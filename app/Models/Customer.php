<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Customer as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Model;


class Customer extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, QueryScopes; //SoftDeletes xoa mem

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'province_id',
        'district_id',
        'ward_id',
        'address',
        'birthday',
        'image',
        'description',
        'customer_agent',
        'ip',
        'customer_catalogue_id',
        'publish',
        'source_id'
    ];
    public function customer_catalogues()
    {
        return $this->belongsTo(CustomerCatalogue::class, 'customer_catalogue_id', 'id');
    }
    public function sources()
    {
        return $this->belongsTo(Source::class, 'source_id', 'id');
    }
    public function hasPermission($permissionCanonical)
    {
        return $this->customer_catalogues->permissions->contains('canonical', $permissionCanonical);
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $attributes = [
        'publish' => 2
    ];
}
