<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use Illuminate\Http\Request;
use App\Models\CustomerCatalogue;
// use App\Models\Customer;
use App\Services\Interfaces\CustomerServiceInterface as CustomerService;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface as CustomerRepository;
use App\Repositories\Interfaces\SourceRepositoryInterface as SourceRepository;

//Neu muon view hieu duoc controller thi phai compact
class CustomerController extends Controller
{
    protected $customerService;
    protected $provinceRepository;
    protected $customerRepository;
    protected $sourceRepository;
    public function __construct(
        CustomerService $customerService,
        ProvinceRepository $provinceRepository,
        CustomerRepository $customerRepository,
        SourceRepository $sourceRepository,
    ) {
        $this->customerService = $customerService;
        $this->provinceRepository = $provinceRepository;
        $this->customerRepository = $customerRepository;
        $this->sourceRepository = $sourceRepository;
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', 'customer.index');
            // dd($request);
            $customerCatalogues = CustomerCatalogue::where('publish', 2)->get();
            $customers = $this->customerService->paginate($request);
            // dd($customers); //hien thi thanh vien
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'Customer'
            ];
            $config['seo'] = __('message.customer');
            // dd($config['seo']);
            $template = 'backend.customer.customer.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'customers', 'customerCatalogues'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'customer.create');
            $provinces = $this->provinceRepository->all();
            $sources = $this->sourceRepository->all();
            $customerCatalogues = CustomerCatalogue::where('publish', 2)->get(); // Lấy danh sách nhóm thành viên đã được publish
            $config = $this->configData();
            $config['seo'] = __('message.customer');
            $config['method'] = 'create';
            $template = 'backend.customer.customer.store';

            // Truyền customerCatalogues vào view
            return view('backend.dashboard.layout', compact('template', 'config', 'provinces', 'customerCatalogues', 'sources'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreCustomerRequest $request)
    {
        if ($this->customerService->create($request)) {
            return redirect()->route('customer.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('customer.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'customer.update');
            $customer = $this->customerRepository->findById($id);
            $sources = $this->sourceRepository->all();
            // dd($customer);
            $provinces = $this->provinceRepository->all();
            $customerCatalogues = CustomerCatalogue::where('publish', 2)->get();
            // dd($provinces);
            // dd($province);
            $config = $this->configData();
            $template = 'backend.customer.customer.store';

            $config['seo'] = __('message.customer');
            $config['method'] = 'edit';
            return view('backend.dashboard.layout', compact('template', 'config', 'provinces', 'customer', 'customerCatalogues', 'sources'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function update($id, UpdateCustomerRequest $request)
    {
        if ($this->customerService->update($id, $request)) {
            return redirect()->route('customer.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('customer.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'customer.destroy');
            $config['seo'] = __('message.customer');
            $customer = $this->customerRepository->findById($id);
            $template = 'backend.customer.customer.delete';
            return view('backend.dashboard.layout', compact('template', 'customer', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->customerService->destroy($id)) {
            return redirect()->route('customer.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('customer.index')->with('error', 'Xóa bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'css' => ['backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'],
            'js' => [
                'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'backend/plugin/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
            ],

        ];
    }
}
