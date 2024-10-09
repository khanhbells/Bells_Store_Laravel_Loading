<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\SystemServiceInterface as SystemService;
use App\Repositories\Interfaces\SystemRepositoryInterface as SystemRepository;
use Illuminate\Http\Request;
use App\Classes\System;
use App\Models\Language;

class SystemController extends Controller
{
    protected $systemLibrary;
    protected $systemService;
    protected $systemRepository;
    protected $language;
    public function __construct(System $systemLibrary, SystemService $systemService, SystemRepository $systemRepository)
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
        $this->systemLibrary = $systemLibrary;
        $this->systemService = $systemService;
        $this->systemRepository = $systemRepository;
    }
    public function index()
    {
        $systemConfig = $this->systemLibrary->config();

        $systems = convert_array(
            $this->systemRepository->findByCondition(
                [
                    ['language_id', '=', $this->language]
                ],
                TRUE
            ),
            'keyword',
            'content'
        );


        $config = $this->config();
        $config['seo'] = __('message.system');
        $template = 'backend.system.index';
        return view('backend.dashboard.layout', compact('template', 'config', 'systemConfig', 'systems'));
    }
    public function store(Request $request)
    {
        // dd($request);
        if ($this->systemService->save($request, $this->language)) {
            return redirect()->route('system.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('system.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function translate($languageId = 0)
    {
        $systemConfig = $this->systemLibrary->config();

        $systems = convert_array(
            $this->systemRepository->findByCondition(
                [
                    ['language_id', '=', $languageId]
                ],
                TRUE
            ),
            'keyword',
            'content'
        );
        $config = $this->config();
        $config['seo'] = __('message.system');
        $config['method'] = 'translates';
        $template = 'backend.system.index';
        return view('backend.dashboard.layout', compact('template', 'config', 'systemConfig', 'systems', 'languageId'));;
    }
    public function saveTranslate(Request $request, $languageId)
    {
        if ($this->systemService->save($request, $languageId)) {
            return redirect()->route('system.translate', ['languageId' => $languageId])->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('system.translate')->with('error', 'Cập nhật bản ghi không thành công');
    }

    private function config()
    {
        return [
            'js' => [
                'backend/plugin/ckeditor/ckeditor.js',
                'backend/plugin/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
            ]
        ];
    }
}
