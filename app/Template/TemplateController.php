<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store{ModuleTemplate}Request;
use App\Http\Requests\Update{ModuleTemplate}Request;
use App\Http\Requests\Delete{ModuleTemplate}Request;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Services\Interfaces\{ModuleTemplate}ServiceInterface as {ModuleTemplate}Service;
use App\Repositories\Interfaces\{ModuleTemplate}RepositoryInterface as {ModuleTemplate}Repository;
use App\Classes\Nestedsetbie;
use App\Models\Language;
use App\Models\{ModuleTemplate};

// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class {ModuleTemplate}Controller extends Controller
{
    protected ${moduleTemplate}Service;
    protected ${moduleTemplate}Repository;
    protected $nestedset;
    protected $language;
    public function __construct({ModuleTemplate}Service ${moduleTemplate}Service, {ModuleTemplate}Repository ${moduleTemplate}Repository)
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->{moduleTemplate}Service = ${moduleTemplate}Service;
        $this->{moduleTemplate}Repository = ${moduleTemplate}Repository;
        $this->initialize();
    }
    public function initialize()
    {
        $this->nestedset = new Nestedsetbie([
            'table' => '{tableName}',
            'foreignkey' => '{foreignKey}',
            'language_id' => $this->language,
        ]);
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', '{moduleView}.index');
            $languageId = $this->language;
            ${moduleTemplate}s = $this->{moduleTemplate}Service->paginate($request, $this->language);
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => '{ModuleTemplate}'
            ];
            $config['seo'] = __('message.{moduleTemplate}');
            $template = 'backend.{moduleTemplate}.{moduleTemplate}.index';
            $dropdown = $this->nestedset->Dropdown();
            return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', '{moduleTemplate}s', 'languageId'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', '{moduleTemplate}.create');
            $config = $this->configData();
            $config['seo'] = __('message.{moduleTemplate}');
            $config['method'] = 'create';
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.{moduleTemplate}.{moduleTemplate}.store';
            return view('backend.dashboard.layout', compact('template', 'config', 'dropdown'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(Store{ModuleTemplate}Request $request)
    {
        // dd($request);
        if ($this->{moduleTemplate}Service->create($request, $this->language)) {
            return redirect()->route('{moduleTemplate}.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('{moduleTemplate}.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', '{moduleTemplate}.update');
            ${moduleTemplate} = $this->{moduleTemplate}Repository->get{ModuleTemplate}ById($id, $this->language);
            $config = $this->configData();
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.{moduleTemplate}.{moduleTemplate}.store';
            $config['seo'] = __('message.{moduleTemplate}');
            $config['method'] = 'edit';
            return view('backend.dashboard.layout', compact('template', 'config', '{moduleTemplate}', 'dropdown'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    //--------------------------------------------------------------
    public function update($id, Update{ModuleTemplate}Request $request)
    {
        if ($this->{moduleTemplate}Service->update($id, $request, $this->language)) {
            return redirect()->route('{moduleTemplate}.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('{moduleTemplate}.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', '{moduleTemplate}.destroy');
            $config['seo'] = __('message.{moduleTemplate}');
            ${moduleTemplate} = $this->{moduleTemplate}Repository->get{moduleTemplate}ById($id, $this->language);
            $template = 'backend.{moduleTemplate}.{moduleTemplate}.delete';
            return view('backend.dashboard.layout', compact('template', '{moduleTemplate}', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->{moduleTemplate}Service->destroy($id)) {
            return redirect()->route('{moduleTemplate}.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('{moduleTemplate}.index')->with('error', 'Xóa bản ghi không thành công');
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
