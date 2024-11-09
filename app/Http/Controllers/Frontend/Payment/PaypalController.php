<?php

namespace App\Http\Controllers\Frontend\Payment;

use App\Http\Controllers\FrontendController;
use Exception;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use App\Services\Interfaces\OrderRepositoryInterface as OrderService;
use App\Services\OrderService as ServicesOrderService;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends FrontendController
{
    protected $orderRepository;
    protected $orderService;
    public function __construct(
        OrderRepository $orderRepository,
        ServicesOrderService $orderService
    ) {
        parent::__construct();
        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
    }

    public function success(Request $request)
    {
        $system = $this->system;
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request->token);
        $orderId = $request->input('orderId');

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $order = $this->orderRepository->findByCondition([
                ['id', '=', $orderId],
            ], false, ['products']);
            $seo = [
                'meta_title' => 'Thông tin thanh toán mã đơn hàng #' . $orderId . '',
                'meta_keyword' => '',
                'meta_description' => '',
                'meta_image' => '',
                'canonical' => write_url('cart/success', true, true)
            ];
            $payload['payment'] = 'paid';
            $payload['confirm'] = 'confirm';
            $flag = $this->orderService->updatePaymentOnline($payload, $order);
            $template = 'frontend.cart.component.paypal';
            return view('frontend.cart.success', compact(
                'seo',
                'system',
                'order',
                'template',
            ));
        }
    }
    public function cancel(Request $request)
    {
        echo 'Hủy thanh toán thành công';
        die();
    }
}
