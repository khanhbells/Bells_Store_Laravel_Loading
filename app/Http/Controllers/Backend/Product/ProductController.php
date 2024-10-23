<?php

namespace App\Http\Controllers\Backend\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\DeleteProductRequest;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface as AttributeCatalogueRepository;
use App\Classes\Nestedsetbie;
use App\Models\Language;
use App\Models\Product;

// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class ProductController extends Controller
{
    protected $productService;
    protected $productRepository;
    protected $nestedset;
    protected $language;
    protected $attributeCatalogueRepository;
    public function __construct(ProductService $productService, ProductRepository $productRepository, AttributeCatalogueRepository $attributeCatalogueRepository)
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
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
            $this->authorize('modules', 'product.index');
            $languageId = $this->language;
            $products = $this->productService->paginate($request, $this->language);
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'Product'
            ];
            $config['seo'] = __('message.product');
            $template = 'backend.product.product.index';
            $dropdown = $this->nestedset->Dropdown();
            return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', 'products', 'languageId'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'product.create');
            $attributeCatalogue = $this->attributeCatalogueRepository->getAll($this->language);
            $config = $this->configData();
            $config['seo'] = __('message.product');
            $config['method'] = 'create';
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.product.product.store';
            return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', 'attributeCatalogue'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreProductRequest $request)
    {
        // dd($request);
        if ($this->productService->create($request, $this->language)) {
            return redirect()->route('product.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('product.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'product.update');
            $product = $this->productRepository->getProductById($id, $this->language);
            $attributeCatalogue = $this->attributeCatalogueRepository->getAll($this->language);
            $config = $this->configData();
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.product.product.store';
            $config['seo'] = __('message.product');
            $config['method'] = 'edit';
            $album = json_decode($product->album);
            return view('backend.dashboard.layout', compact('template', 'config', 'product', 'dropdown', 'album', 'attributeCatalogue'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    //--------------------------------------------------------------
    public function update($id, UpdateProductRequest $request)
    {
        if ($this->productService->update($id, $request, $this->language)) {
            return redirect()->route('product.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('product.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'product.destroy');
            $config['seo'] = __('message.product');
            $product = $this->productRepository->getProductById($id, $this->language);
            $template = 'backend.product.product.delete';
            return view('backend.dashboard.layout', compact('template', 'product', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->productService->destroy($id)) {
            return redirect()->route('product.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('product.index')->with('error', 'Xóa bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'js' => [
                'backend/plugin/ckeditor/ckeditor.js',
                'backend/plugin/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'backend/library/variant.js',
                'backend/js/plugins/switchery/switchery.js',
                'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugin/nice-select/js/jquery.nice-select.min.js'
            ],
            'css' => [
                'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css',
                'backend/plugin/nice-select/css/nice-select.css',
                'backend/css/plugins/switchery/switchery.css',
            ]

        ];
    }
}
