<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use Illuminate\Http\Request;
// use App\Models\Order;
use App\Services\Interfaces\OrderServiceInterface as OrderService;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;

//Neu muon view hieu duoc controller thi phai compact
class OrderController extends Controller
{
    protected $orderService;
    protected $orderRepository;
    public function __construct(OrderService $orderService, OrderRepository $orderRepository)
    {
        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', 'order.index');
            // dd($request);
            $orders = $this->orderService->paginate($request);
            // dd($orders); //hien thi thanh vien
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js',
                    'backend/js/plugins/daterangepicker/daterangepicker.js',
                    'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'backend/css/plugins/daterangepicker/daterangepicker-bs3.css',
                    'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'Order'
            ];
            $config['seo'] = __('message.order');
            // dd($config['seo']);
            $template = 'backend.order.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'orders'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function detail(Request $request, $id)
    {
        $order = $this->orderRepository->getOrderById($id, ['products']);
        $order = $this->orderService->getOrderItemImage($order);
        $config['seo'] = __('message.order');
        // dd($config['seo']);
        $template = 'backend.order.detail';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'order',
        ));
    }
}
