<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store{class}CatalogueRequest;
use App\Http\Requests\Update{class}CatalogueRequest;
use App\Http\Requests\Delete{class}CatalogueRequest;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Services\Interfaces\{class}CatalogueServiceInterface as {class}CatalogueService;
use App\Repositories\Interfaces\{class}CatalogueRepositoryInterface as {class}CatalogueRepository;
use App\Classes\Nestedsetbie;
use App\Models\Language;
// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class {class}CatalogueController extends Controller
{
    protected ${module}CatalogueService;
    protected ${module}CatalogueRepository;
    protected $nestedset;
    protected $language;
    public function __construct({class}CatalogueService ${module}CatalogueService, {class}CatalogueRepository ${module}CatalogueRepository, Nestedsetbie $nestedset)
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->{module}CatalogueService = ${module}CatalogueService;
        $this->{module}CatalogueRepository = ${module}CatalogueRepository;
        $this->initialize();
    }
    public function initialize()
    {
        $this->nestedset = new Nestedsetbie([
            'table' => '{module}_catalogues',
            'foreignkey' => '{module}_catalogue_id',
            'language_id' => $this->language,
        ]);
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', '{module}.catalogue.index');

            ${module}Catalogues = $this->{module}CatalogueService->paginate($request, $this->language);
            // dd(${module}Catalogues); //hien thi thanh vien


            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => '{class}Catalogue'
            ];
            $config['seo'] = __('message.{module}Catalogue');
            // dd($config['seo']);
            $template = 'backend.{module}.catalogue.index';
            return view('backend.dashboard.layout', compact('template', 'config', '{module}Catalogues'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', '{module}.catalogue.create');
            $config = $this->configData();
            $config['seo'] = __('message.{module}Catalogue');
            $config['method'] = 'create';
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.{module}.catalogue.store';
            return view('backend.dashboard.layout', compact('template', 'config', 'dropdown'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(Store{class}CatalogueRequest $request)
    {
        // dd($request);
        if ($this->{module}CatalogueService->create($request, $this->language)) {
            return redirect()->route('{module}.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('{module}.catalogue.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', '{module}.catalogue.update');
            ${module}Catalogue = $this->{module}CatalogueRepository->get{class}CatalogueById($id, $this->language);
            $config = $this->configData();
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.{module}.catalogue.store';
            $config['seo'] = __('message.{module}Catalogue');
            $config['method'] = 'edit';
            $album = json_decode(${module}Catalogue->album);
            return view('backend.dashboard.layout', compact('template', 'config', '{module}Catalogue', 'dropdown', 'album'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    //--------------------------------------------------------------
    public function update($id, Update{class}CatalogueRequest $request)
    {
        // dd($request);
        if ($this->{module}CatalogueService->update($id, $request, $this->language)) {
            return redirect()->route('{module}.catalogue.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('{module}.catalogue.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', '{module}.catalogue.destroy');
            $config['seo'] = __('message.{module}Catalogue');
            ${module}Catalogue = $this->{module}CatalogueRepository->get{class}CatalogueById($id, $this->language);
            $template = 'backend.{module}.catalogue.delete';
            return view('backend.dashboard.layout', compact('template', '{module}Catalogue', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id, Delete{class}CatalogueRequest $request)
    {
        if ($this->{module}CatalogueService->destroy($id, $this->language)) {
            return redirect()->route('{module}.catalogue.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('{module}.catalogue.index')->with('error', 'Xóa bản ghi không thành công');
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
