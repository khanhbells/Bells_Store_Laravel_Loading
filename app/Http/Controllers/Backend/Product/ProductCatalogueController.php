<?php

namespace App\Http\Controllers\Backend\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductCatalogueRequest;
use App\Http\Requests\UpdateProductCatalogueRequest;
use App\Http\Requests\DeleteProductCatalogueRequest;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Classes\Nestedsetbie;
use App\Models\Language;
// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class ProductCatalogueController extends Controller
{
    protected $productCatalogueService;
    protected $productCatalogueRepository;
    protected $nestedset;
    protected $language;
    public function __construct(ProductCatalogueService $productCatalogueService, ProductCatalogueRepository $productCatalogueRepository, Nestedsetbie $nestedset)
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->productCatalogueService = $productCatalogueService;
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->initialize();
    }
    public function initialize()
    {
        $this->nestedset = new Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
            'language_id' => $this->language,
        ]);
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', 'product.catalogue.index');

            $productCatalogues = $this->productCatalogueService->paginate($request, $this->language);
            // dd($productCatalogues); //hien thi thanh vien


            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'ProductCatalogue'
            ];
            $config['seo'] = __('message.productCatalogue');
            // dd($config['seo']);
            $template = 'backend.product.catalogue.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'productCatalogues'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'product.catalogue.create');
            $config = $this->configData();
            $config['seo'] = __('message.productCatalogue');
            $config['method'] = 'create';
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.product.catalogue.store';
            return view('backend.dashboard.layout', compact('template', 'config', 'dropdown'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreProductCatalogueRequest $request)
    {
        // dd($request);
        if ($this->productCatalogueService->create($request, $this->language)) {
            return redirect()->route('product.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('product.catalogue.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'product.catalogue.update');
            $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);
            $config = $this->configData();
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.product.catalogue.store';
            $config['seo'] = __('message.productCatalogue');
            $config['method'] = 'edit';
            $album = json_decode($productCatalogue->album);
            return view('backend.dashboard.layout', compact('template', 'config', 'productCatalogue', 'dropdown', 'album'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    //--------------------------------------------------------------
    public function update($id, UpdateProductCatalogueRequest $request)
    {
        // dd($request);
        if ($this->productCatalogueService->update($id, $request, $this->language)) {
            return redirect()->route('product.catalogue.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('product.catalogue.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'product.catalogue.destroy');
            $config['seo'] = __('message.productCatalogue');
            $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);
            $template = 'backend.product.catalogue.delete';
            return view('backend.dashboard.layout', compact('template', 'productCatalogue', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id, DeleteProductCatalogueRequest $request)
    {
        if ($this->productCatalogueService->destroy($id, $this->language)) {
            return redirect()->route('product.catalogue.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('product.catalogue.index')->with('error', 'Xóa bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'js' => [
                'backend/plugin/ckeditor/ckeditor.js',
                'backend/plugin/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];
    }
}
