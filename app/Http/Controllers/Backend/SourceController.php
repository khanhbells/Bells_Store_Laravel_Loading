<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Source\StoreSourceRequest;
use App\Http\Requests\Source\UpdateSourceRequest;
use App\Models\Language;
use App\Models\Source;
use Illuminate\Http\Request;
use App\Models\SourceCatalogue;
// use App\Models\Source;
use App\Services\Interfaces\SourceServiceInterface as SourceService;
use App\Repositories\Interfaces\SourceRepositoryInterface as SourceRepository;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
use Illuminate\Support\Collection;
//Neu muon view hieu duoc controller thi phai compact
class SourceController extends Controller
{
    protected $sourceService;
    protected $sourceRepository;
    protected $language;
    protected $languageRepository;
    public function __construct(
        SourceService $sourceService,
        SourceRepository $sourceRepository,
        LanguageRepository $languageRepository,
    ) {
        $this->sourceService = $sourceService;
        $this->sourceRepository = $sourceRepository;
        $this->languageRepository = $languageRepository;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', 'source.index');
            // dd($request);
            $sources = $this->sourceService->paginate($request);
            // dd($sources); //hien thi thanh vien
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'Source'
            ];
            $config['seo'] = __('message.source');
            // dd($config['seo']);
            $template = 'backend.source.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'sources'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'source.create');
            $config = $this->configData();
            $config['seo'] = __('message.source');
            $config['method'] = 'create';
            $template = 'backend.source.store';

            // Truyền sourceCatalogues vào view
            return view('backend.dashboard.layout', compact('template', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreSourceRequest $request)
    {
        if ($this->sourceService->create($request, $this->language)) {
            return redirect()->route('source.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('source.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'source.update');
            $source = $this->sourceRepository->findById($id);
            $config = $this->configData();
            $template = 'backend.source.store';
            $config['seo'] = __('message.source');
            $config['method'] = 'edit';
            return view('backend.dashboard.layout', compact('template', 'config', 'source'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    private function menuItemAgrument(array $whereIn = [])
    {
        $language = $this->language;
        return [
            'condition' => [],
            'flag' => true,
            'relation' => [
                'languages' => function ($query) use ($language) {
                    $query->where('language_id', $language);
                }
            ],
            'orderBy' => ['id', 'desc'],
            'param' => [
                'whereIn' => $whereIn,
                'whereInField' => 'id'
            ]
        ];
    }
    public function update($id, UpdateSourceRequest $request)
    {
        if ($this->sourceService->update($id, $request, $this->language)) {
            return redirect()->route('source.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('source.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'source.destroy');
            $config['seo'] = __('message.source');
            $source = $this->sourceRepository->findById($id);
            $template = 'backend.source.delete';
            return view('backend.dashboard.layout', compact('template', 'source', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->sourceService->destroy($id)) {
            return redirect()->route('source.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('source.index')->with('error', 'Xóa bản ghi không thành công');
    }
    public function translate($languageId, $sourceId)
    {
        try {
            $this->authorize('modules', 'source.translate');

            $source = $this->sourceRepository->findById($sourceId);
            $source->jsonDescription = $source->description;
            $source->description = $source->description[$this->language];

            $sourceTranslate = new \stdClass;
            $sourceTranslate->description = ($source->jsonDescription[$languageId]) ?? '';


            $translate = $this->languageRepository->findById($languageId);
            $config = $this->configData();
            $config['seo'] = __('message.source');
            $config['method'] = 'create';
            $template = 'backend.source.translate';

            // Truyền sourceCatalogues vào view
            return view('backend.dashboard.layout', compact(
                'template',
                'config',
                'source',
                'translate',
                'sourceTranslate'
            ));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function saveTranslate(Request $request)
    {
        if ($this->sourceService->saveTranslate($request, $this->language)) {
            return redirect()->route('source.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('source.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'css' => ['backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'],
            'js' => [
                'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugin/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/source.js',
                'backend/plugin/ckeditor/ckeditor.js',
            ],

        ];
    }
}
