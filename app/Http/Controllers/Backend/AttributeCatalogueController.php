<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeCatalogueRequest;
use App\Http\Requests\UpdateAttributeCatalogueRequest;
use App\Http\Requests\DeleteAttributeCatalogueRequest;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Services\Interfaces\AttributeCatalogueServiceInterface as AttributeCatalogueService;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface as AttributeCatalogueRepository;
use App\Classes\Nestedsetbie;
use App\Models\Language;
// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class AttributeCatalogueController extends Controller
{
    protected $attributeCatalogueService;
    protected $attributeCatalogueRepository;
    protected $nestedset;
    protected $language;
    public function __construct(AttributeCatalogueService $attributeCatalogueService, AttributeCatalogueRepository $attributeCatalogueRepository, Nestedsetbie $nestedset)
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->attributeCatalogueService = $attributeCatalogueService;
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
        $this->initialize();
    }
    public function initialize()
    {
        $this->nestedset = new Nestedsetbie([
            'table' => 'attribute_catalogues',
            'foreignkey' => 'attribute_catalogue_id',
            'language_id' => $this->language,
        ]);
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', 'attribute.catalogue.index');

            $attributeCatalogues = $this->attributeCatalogueService->paginate($request, $this->language);
            // dd($attributeCatalogues); //hien thi thanh vien


            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'AttributeCatalogue'
            ];
            $config['seo'] = __('message.attributeCatalogue');
            // dd($config['seo']);
            $template = 'backend.attribute.catalogue.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'attributeCatalogues'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'attribute.catalogue.create');
            $config = $this->configData();
            $config['seo'] = __('message.attributeCatalogue');
            $config['method'] = 'create';
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.attribute.catalogue.store';
            return view('backend.dashboard.layout', compact('template', 'config', 'dropdown'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreAttributeCatalogueRequest $request)
    {
        // dd($request);
        if ($this->attributeCatalogueService->create($request, $this->language)) {
            return redirect()->route('attribute.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('attribute.catalogue.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'attribute.catalogue.update');
            $attributeCatalogue = $this->attributeCatalogueRepository->getAttributeCatalogueById($id, $this->language);
            $config = $this->configData();
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.attribute.catalogue.store';
            $config['seo'] = __('message.attributeCatalogue');
            $config['method'] = 'edit';
            $album = json_decode($attributeCatalogue->album);
            return view('backend.dashboard.layout', compact('template', 'config', 'attributeCatalogue', 'dropdown', 'album'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    //--------------------------------------------------------------
    public function update($id, UpdateAttributeCatalogueRequest $request)
    {
        // dd($request);
        if ($this->attributeCatalogueService->update($id, $request, $this->language)) {
            return redirect()->route('attribute.catalogue.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('attribute.catalogue.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'attribute.catalogue.destroy');
            $config['seo'] = __('message.attributeCatalogue');
            $attributeCatalogue = $this->attributeCatalogueRepository->getAttributeCatalogueById($id, $this->language);
            $template = 'backend.attribute.catalogue.delete';
            return view('backend.dashboard.layout', compact('template', 'attributeCatalogue', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id, DeleteAttributeCatalogueRequest $request)
    {
        if ($this->attributeCatalogueService->destroy($id, $this->language)) {
            return redirect()->route('attribute.catalogue.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('attribute.catalogue.index')->with('error', 'Xóa bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'js' => [
                'backend/plugin/ckeditor/ckeditor.js',
                'backend/plugin/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];
    }
}
