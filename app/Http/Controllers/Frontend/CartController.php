<?php

namespace App\Http\Controllers\Frontend;

use App\Classes\Vnpay;
use App\Classes\Momo;
use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Enums\SlideEnum;
use App\Repositories\SystemRepository;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use App\Http\Requests\StoreCartRequest;
use App\Services\Interfaces\CartServiceInterface as CartService;
use App\Mail\OrderMail;
use Illuminate\Support\Facades\Mail;

// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class CartController extends FrontendController
{
    protected $provinceRepository;
    protected $cartService;
    protected $promotionRepository;
    protected $orderRepository;
    protected $vnpay;
    protected $momo;
    public function __construct(
        ProvinceRepository $provinceRepository,
        CartService $cartService,
        PromotionRepository $promotionRepository,
        OrderRepository $orderRepository,
        Vnpay $vnpay,
        Momo $momo,
    ) {
        parent::__construct();
        $this->provinceRepository = $provinceRepository;
        $this->cartService = $cartService;
        $this->promotionRepository = $promotionRepository;
        $this->orderRepository = $orderRepository;
        $this->vnpay = $vnpay;
        $this->momo = $momo;
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
        $system = $this->system;
        $order = $this->cartService->order($request, $system);
        if ($order['flag']) {
            $response = $this->paymentMethod($order);
            if ($response['errorCode'] == 0) {
                return redirect()->away($response['url']);
            }
            return redirect()->route('cart.success', ['code' => $order['order']->code])->with('success', 'Đặt hàng thành công');
        }
        return redirect()->route('cart.checkout')->with('error', 'Đặt hàng không thành công. Hãy thử lại!');
    }

    public function success($code)
    {

        $order = $this->orderRepository->findByCondition([
            ['code', '=', $code],
        ], false, ['products']);
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
            'order',
        ));
    }

    public function paymentMethod($order = null)
    {
        switch ($order['order']->method) {
            case 'vnpay':
                $response = $this->vnpay->payment($order['order']);
                break;
            case 'momo':
                $response = $this->momo->payment($order['order']);
                break;

            default:
                # code...
                break;
        }
        return $response;
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
