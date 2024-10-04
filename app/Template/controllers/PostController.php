<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store{class}Request;
use App\Http\Requests\Update{class}Request;
use App\Http\Requests\Delete{class}Request;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Services\Interfaces\{class}ServiceInterface as {class}Service;
use App\Repositories\Interfaces\{class}RepositoryInterface as {class}Repository;
use App\Classes\Nestedsetbie;
use App\Models\Language;
use App\Models\{class};

// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class {class}Controller extends Controller
{
    protected ${module}Service;
    protected ${module}Repository;
    protected $nestedset;
    protected $language;
    public function __construct({class}Service ${module}Service, {class}Repository ${module}Repository)
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->{module}Service = ${module}Service;
        $this->{module}Repository = ${module}Repository;
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
            $this->authorize('modules', '{module}.index');
            $languageId = $this->language;
            ${module}s = $this->{module}Service->paginate($request, $this->language);
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => '{class}'
            ];
            $config['seo'] = __('message.{module}');
            $template = 'backend.{module}.{module}.index';
            $dropdown = $this->nestedset->Dropdown();
            return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', '{module}s', 'languageId'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', '{module}.create');
            $config = $this->configData();
            $config['seo'] = __('message.{module}');
            $config['method'] = 'create';
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.{module}.{module}.store';
            return view('backend.dashboard.layout', compact('template', 'config', 'dropdown'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(Store{class}Request $request)
    {
        // dd($request);
        if ($this->{module}Service->create($request, $this->language)) {
            return redirect()->route('{module}.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('{module}.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', '{module}.update');
            ${module} = $this->{module}Repository->get{class}ById($id, $this->language);
            $config = $this->configData();
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.{module}.{module}.store';
            $config['seo'] = __('message.{module}');
            $config['method'] = 'edit';
            $album = json_decode(${module}->album);
            return view('backend.dashboard.layout', compact('template', 'config', '{module}', 'dropdown', 'album'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    //--------------------------------------------------------------
    public function update($id, Update{class}Request $request)
    {
        if ($this->{module}Service->update($id, $request, $this->language)) {
            return redirect()->route('{module}.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('{module}.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', '{module}.destroy');
            $config['seo'] = __('message.{module}');
            ${module} = $this->{module}Repository->get{class}ById($id, $this->language);
            $template = 'backend.{module}.{module}.delete';
            return view('backend.dashboard.layout', compact('template', '{module}', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->{module}Service->destroy($id)) {
            return redirect()->route('{module}.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('{module}.index')->with('error', 'Xóa bản ghi không thành công');
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
    public function catalogue(${module})
    {
        dd(${module}->{module}_catalogues);
        // foreach(${module} as $key->$val)
    }
}
