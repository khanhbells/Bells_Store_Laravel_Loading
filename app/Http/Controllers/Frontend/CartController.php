<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Enums\SlideEnum;
use App\Repositories\SystemRepository;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Services\Interfaces\CartServiceInterface as CartService;

// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class CartController extends FrontendController
{
    protected $provinceRepository;
    protected $cartService;
    public function __construct(ProvinceRepository $provinceRepository, CartService $cartService)
    {
        parent::__construct();
        $this->provinceRepository = $provinceRepository;
        $this->cartService = $cartService;
    }
    public function checkout()
    {
        // Cart::instance('shopping')->destroy();
        $provinces = $this->provinceRepository->all();
        $carts = Cart::instance('shopping')->content();
        $carts = $this->cartService->remakeCart($carts);
        $cartConfig = $this->cartConfig();
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
            'cartConfig'
        ));
    }
    private function cartConfig()
    {
        return [
            'cartTotal' => $cartTotal = Cart::instance('shopping')->total(),
        ];
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
