<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Http\Requests\DeleteAttributeRequest;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Services\Interfaces\AttributeServiceInterface as AttributeService;
use App\Repositories\Interfaces\AttributeRepositoryInterface as AttributeRepository;
use App\Classes\Nestedsetbie;
use App\Models\Language;
use App\Models\Attribute;

// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class AttributeController extends Controller
{
    protected $attributeService;
    protected $attributeRepository;
    protected $nestedset;
    protected $language;
    public function __construct(AttributeService $attributeService, AttributeRepository $attributeRepository)
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->attributeService = $attributeService;
        $this->attributeRepository = $attributeRepository;
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
            $this->authorize('modules', 'attribute.index');
            $languageId = $this->language;
            $attributes = $this->attributeService->paginate($request, $this->language);
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'Attribute'
            ];
            $config['seo'] = __('message.attribute');
            $template = 'backend.attribute.attribute.index';
            $dropdown = $this->nestedset->Dropdown();
            return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', 'attributes', 'languageId'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'attribute.create');
            $config = $this->configData();
            $config['seo'] = __('message.attribute');
            $config['method'] = 'create';
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.attribute.attribute.store';
            return view('backend.dashboard.layout', compact('template', 'config', 'dropdown'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreAttributeRequest $request)
    {
        // dd($request);
        if ($this->attributeService->create($request, $this->language)) {
            return redirect()->route('attribute.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('attribute.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'attribute.update');
            $attribute = $this->attributeRepository->getAttributeById($id, $this->language);
            $config = $this->configData();
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.attribute.attribute.store';
            $config['seo'] = __('message.attribute');
            $config['method'] = 'edit';
            $album = json_decode($attribute->album);
            return view('backend.dashboard.layout', compact('template', 'config', 'attribute', 'dropdown', 'album'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    //--------------------------------------------------------------
    public function update($id, UpdateAttributeRequest $request)
    {
        if ($this->attributeService->update($id, $request, $this->language)) {
            return redirect()->route('attribute.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('attribute.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'attribute.destroy');
            $config['seo'] = __('message.attribute');
            $attribute = $this->attributeRepository->getAttributeById($id, $this->language);
            $template = 'backend.attribute.attribute.delete';
            return view('backend.dashboard.layout', compact('template', 'attribute', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->attributeService->destroy($id)) {
            return redirect()->route('attribute.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('attribute.index')->with('error', 'Xóa bản ghi không thành công');
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
    public function catalogue($attribute)
    {
        dd($attribute->attribute_catalogues);
        // foreach($attribute as $key->$val)
    }
}
