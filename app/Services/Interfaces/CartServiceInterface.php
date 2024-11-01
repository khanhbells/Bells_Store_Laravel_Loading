<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface CartServiceInterface extends BaseServiceInterface
{
    public function create(Request $request);
    public function update(Request $request);
    public function delete(Request $request);
    public function reCaculateCart();
    public function order($request, $system);
    public function cartPromotion($cartTotal);
    public function remakeCart($carts);
}
