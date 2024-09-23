<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Services\Interfaces\LanguageServiceInterface as LanguageService;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
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
        $languages = $this->languageService->paginate($request);
        // dd($languages); //hien thi thanh vien


        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];
        $config['seo'] = config('app.language');
        // dd($config['seo']);
        $template = 'backend.language.index';
        return view('backend.dashboard.layout', compact('template', 'config', 'languages'));
    }
    public function create()
    {
        // dd($provinces);
        // dd($province);
        $config = $this->configData();
        $config['seo'] = config('app.language');
        $config['method'] = 'create';
        $template = 'backend.language.store';
        return view('backend.dashboard.layout', compact('template', 'config'));
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
        $language = $this->languageRepository->findById($id);
        $config = $this->configData();
        // dd($language);
        // dd($provinces);
        // dd($province);
        $template = 'backend.language.store';

        $config['seo'] = config('app.language');
        $config['method'] = 'edit';
        return view('backend.dashboard.layout', compact('template', 'config', 'language'));
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
        $config['seo'] = config('app.language');
        $language = $this->languageRepository->findById($id);
        $template = 'backend.language.delete';
        return view('backend.dashboard.layout', compact('template', 'language', 'config'));
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
}
