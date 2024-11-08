<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface OrderServiceInterface
 * @package App\Services\Interfaces
 */
interface OrderServiceInterface
{
    public function paginate($request);
    public function getOrderItemImage($order);
    public function update($request);
}
