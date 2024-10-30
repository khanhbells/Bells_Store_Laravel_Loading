<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Services\Interfaces\CartServiceInterface  as CartService;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends FrontendController
{
    protected $cartService;
    public function __construct(
        CartService $cartService,
    ) {
        parent::__construct();
        $this->cartService = $cartService;
    }

    public function create(Request $request)
    {
        $flag = $this->cartService->create($request, $this->language);
        $cart = Cart::instance('shopping')->content();
        return response()->json([
            'cart' => $cart,
            'messages' => 'Thêm sản phẩm vào giỏ hàng thành công',
            'code' => ($flag) ? 10 : 11
        ]);
    }

    public function update(Request $request)
    {
        $response = $this->cartService->update($request);
        return response()->json([
            'response' => $response,
            'messages' => 'Cập nhật số lượng thành công',
            'code' => (!$response) ? 11 : 10
        ]);
    }
    public function delete(Request $request)
    {
        $response = $this->cartService->delete($request);
        return response()->json([
            'response' => $response,
            'messages' => 'Xóa sản phẩm khỏi giỏ hàng thành công',
            'code' => (!$response) ? 11 : 10
        ]);
    }
}
