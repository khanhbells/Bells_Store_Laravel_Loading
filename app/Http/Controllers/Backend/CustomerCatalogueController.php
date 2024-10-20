<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerCatalogueRequest;
use Illuminate\Http\Request;
// use App\Models\Customer;
use App\Services\Interfaces\CustomerCatalogueServiceInterface as CustomerCatalogueService;
use App\Repositories\Interfaces\CustomerCatalogueRepositoryInterface as CustomerCatalogueRepository;
// use App\Repositories\Interfaces\CustomerCatalogueRepositoryInterface as CustomerCatalogueRepository;
//Neu muon view hieu duoc controller thi phai compact
class CustomerCatalogueController extends Controller
{
    protected $CustomerCatalogueService;
    protected $CustomerCatalogueRepository;
    public function __construct(CustomerCatalogueService $CustomerCatalogueService, CustomerCatalogueRepository $CustomerCatalogueRepository)
    {
        $this->CustomerCatalogueService = $CustomerCatalogueService;
        $this->CustomerCatalogueRepository = $CustomerCatalogueRepository;
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', 'customer.catalogue.index');
            $customerCatalogues = $this->CustomerCatalogueService->paginate($request);
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
                'model' => 'CustomerCatalogue'
            ];
            $config['seo'] = __('message.customerCatalogue');
            // dd($config['seo']);
            $template = 'backend.customer.catalogue.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'customerCatalogues'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'customer.catalogue.create');
            // dd($provinces);
            // dd($province);
            $config['seo'] = __('message.customerCatalogue');
            $config['method'] = 'create';
            $template = 'backend.customer.catalogue.store';
            return view('backend.dashboard.layout', compact('template', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreCustomerCatalogueRequest $request)
    {
        if ($this->CustomerCatalogueService->create($request)) {
            return redirect()->route('customer.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('customer.catalogue.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'customer.catalogue.update');
            $customerCatalogue = $this->CustomerCatalogueRepository->findById($id);
            // dd($customer);
            // dd($provinces);
            // dd($province);
            $template = 'backend.customer.catalogue.store';

            $config['seo'] = __('message.customerCatalogue');
            $config['method'] = 'edit';
            return view('backend.dashboard.layout', compact('template', 'config', 'customerCatalogue'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function update($id, StoreCustomerCatalogueRequest $request)
    {
        if ($this->CustomerCatalogueService->update($id, $request)) {
            return redirect()->route('customer.catalogue.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('customer.catalogue.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'customer.catalogue.destroy');
            $config['seo'] = __('message.customerCatalogue');
            $customerCatalogue = $this->CustomerCatalogueRepository->findById($id);
            $template = 'backend.customer.catalogue.delete';
            return view('backend.dashboard.layout', compact('template', 'customerCatalogue', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->CustomerCatalogueService->destroy($id)) {
            return redirect()->route('customer.catalogue.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('customer.catalogue.index')->with('error', 'Xóa bản ghi không thành công');
    }
}
