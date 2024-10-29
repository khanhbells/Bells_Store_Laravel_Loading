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
    public function remakeCart($carts);
}
