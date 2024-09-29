<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Services\Interfaces\LanguageServiceInterface as LanguageService;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
use Illuminate\Support\Facades\App;
// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class LanguageController extends Controller
{
    protected $languageService;
    protected $languageRepository;
    public function __construct(LanguageService $languageService, LanguageRepository $languageRepository)
    {
        $this->languageService = $languageService;
        $this->languageRepository = $languageRepository;
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', 'language.index');
            $languages = $this->languageService->paginate($request);
            // dd($languages); //hien thi thanh vienƯ
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'Language'
            ];
            $config['seo'] = config('app.language');
            // dd($config['seo']);
            $template = 'backend.language.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'languages'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'language.create');
            // dd($provinces);
            // dd($province);
            $config = $this->configData();
            $config['seo'] = config('app.language');
            $config['method'] = 'create';
            $template = 'backend.language.store';
            return view('backend.dashboard.layout', compact('template', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreLanguageRequest $request)
    {
        if ($this->languageService->create($request)) {
            return redirect()->route('language.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('language.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'language.update');
            $language = $this->languageRepository->findById($id);
            $config = $this->configData();
            // dd($language);
            // dd($provinces);
            // dd($province);
            $template = 'backend.language.store';

            $config['seo'] = config('app.language');
            $config['method'] = 'edit';
            return view('backend.dashboard.layout', compact('template', 'config', 'language'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function update($id, UpdateLanguageRequest $request)
    {
        if ($this->languageService->update($id, $request)) {
            return redirect()->route('language.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('language.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'language.destroy');
            $config['seo'] = config('app.language');
            $language = $this->languageRepository->findById($id);
            $template = 'backend.language.delete';
            return view('backend.dashboard.layout', compact('template', 'language', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->languageService->destroy($id)) {
            return redirect()->route('language.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('language.index')->with('error', 'Xóa bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'js' => [
                'backend/plugin/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
            ],
        ];
    }
    public function switchBackendLanguage($id)
    {
        $language = $this->languageRepository->findById($id);
        if ($this->languageService->switch($id)) {
            session(['app_locale' => $language->canonical]);
            App::setLocale($language->canonical);
        }
        return redirect()->back();
    }
}
