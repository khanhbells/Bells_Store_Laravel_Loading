<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Enums\SlideEnum;
use App\Repositories\SystemRepository;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Http\Requests\StoreCartRequest;
use App\Services\Interfaces\CartServiceInterface as CartService;

// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class CartController extends FrontendController
{
    protected $provinceRepository;
    protected $cartService;
    protected $promotionRepository;
    public function __construct(
        ProvinceRepository $provinceRepository,
        CartService $cartService,
        PromotionRepository $promotionRepository,
    ) {
        parent::__construct();
        $this->provinceRepository = $provinceRepository;
        $this->cartService = $cartService;
        $this->promotionRepository = $promotionRepository;
    }
    public function checkout()
    {
        // Cart::instance('shopping')->destroy();
        $provinces = $this->provinceRepository->all();
        $carts = Cart::instance('shopping')->content();
        $carts = $this->cartService->remakeCart($carts);
        $cartCaculate = $this->cartService->reCaculateCart();
        $cartPromotion = $this->cartService->cartPromotion($cartCaculate['cartTotal']);
        $system = $this->system;
        $seo = [
            'meta_title' => 'Trang thanh toán đơn hàng',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => write_url('thanh-toan', true, true)
        ];
        $config = $this->config();
        return view('frontend.cart.index', compact(
            'config',
            'seo',
            'system',
            'provinces',
            'carts',
            'cartPromotion',
            'cartCaculate'
        ));
    }

    public function store(StoreCartRequest $request)
    {
        $order = $this->cartService->order($request);

        if ($order['flag']) {
            return redirect()->route('cart.success', ['code' => $order['order']->code])->with('success', 'Đặt hàng thành công');
        }
        return redirect()->route('cart.checkout')->with('error', 'Đặt hàng không thành công. Hãy thử lại!');
    }

    public function success($code)
    {
        $config = $this->config();
        $system = $this->system;
        $seo = [
            'meta_title' => 'Thanh toán đơn hàng thành công',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => write_url('cart/success', true, true)
        ];
        return view('frontend.cart.success', compact(
            'config',
            'seo',
            'system',
        ));
    }

    public function config()
    {
        return [
            'language' => $this->language,
            'css' => ['backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'],
            'js' => [
                'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'frontend/core/library/cart.js',
            ]
        ];
    }
}
