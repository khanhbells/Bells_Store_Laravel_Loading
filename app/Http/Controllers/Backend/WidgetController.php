<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWidgetRequest;
use App\Http\Requests\UpdateWidgetRequest;
use App\Models\Language;
use App\Models\Widget;
use Illuminate\Http\Request;
use App\Models\WidgetCatalogue;
// use App\Models\Widget;
use App\Services\Interfaces\WidgetServiceInterface as WidgetService;
use App\Repositories\Interfaces\WidgetRepositoryInterface as WidgetRepository;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
use Illuminate\Support\Collection;
//Neu muon view hieu duoc controller thi phai compact
class WidgetController extends Controller
{
    protected $widgetService;
    protected $widgetRepository;
    protected $language;
    protected $languageRepository;
    public function __construct(
        WidgetService $widgetService,
        WidgetRepository $widgetRepository,
        LanguageRepository $languageRepository,
    ) {
        $this->widgetService = $widgetService;
        $this->widgetRepository = $widgetRepository;
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
            $this->authorize('modules', 'widget.index');
            // dd($request);
            $widgets = $this->widgetService->paginate($request);
            // dd($widgets); //hien thi thanh vien
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'Widget'
            ];
            $config['seo'] = __('message.widget');
            // dd($config['seo']);
            $template = 'backend.widget.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'widgets'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'widget.create');
            $config = $this->configData();
            $config['seo'] = __('message.widget');
            $config['method'] = 'create';
            $template = 'backend.widget.store';

            // Truyền widgetCatalogues vào view
            return view('backend.dashboard.layout', compact('template', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreWidgetRequest $request)
    {
        if ($this->widgetService->create($request, $this->language)) {
            return redirect()->route('widget.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'widget.update');
            $widget = $this->widgetRepository->findById($id);
            $widget->description = $widget->description[$this->language];
            $modelClass = loadClass($widget->model);
            $widgetItem = convertArrayByKey($modelClass->findByCondition(
                ...array_values($this->menuItemAgrument($widget->model_id))
            ), ['id', 'name.languages', 'image']);

            $config = $this->configData();
            $template = 'backend.widget.store';
            $album = $widget->album;
            $config['seo'] = __('message.widget');
            $config['method'] = 'edit';
            return view('backend.dashboard.layout', compact('template', 'config', 'widget', 'album', 'widgetItem'));
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
    public function update($id, UpdateWidgetRequest $request)
    {
        if ($this->widgetService->update($id, $request, $this->language)) {
            return redirect()->route('widget.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'widget.destroy');
            $config['seo'] = __('message.widget');
            $widget = $this->widgetRepository->findById($id);
            $template = 'backend.widget.delete';
            return view('backend.dashboard.layout', compact('template', 'widget', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->widgetService->destroy($id)) {
            return redirect()->route('widget.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('error', 'Xóa bản ghi không thành công');
    }
    public function translate($languageId, $widgetId)
    {
        try {
            $this->authorize('modules', 'widget.translate');

            $widget = $this->widgetRepository->findById($widgetId);
            $widget->jsonDescription = $widget->description;
            $widget->description = $widget->description[$this->language];

            $widgetTranslate = new \stdClass;
            $widgetTranslate->description = ($widget->jsonDescription[$languageId]) ?? '';


            $translate = $this->languageRepository->findById($languageId);
            $config = $this->configData();
            $config['seo'] = __('message.widget');
            $config['method'] = 'create';
            $template = 'backend.widget.translate';

            // Truyền widgetCatalogues vào view
            return view('backend.dashboard.layout', compact(
                'template',
                'config',
                'widget',
                'translate',
                'widgetTranslate'
            ));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function saveTranslate(Request $request)
    {
        if ($this->widgetService->saveTranslate($request, $this->language)) {
            return redirect()->route('widget.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'css' => ['backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'],
            'js' => [
                'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugin/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/widget.js',
                'backend/plugin/ckeditor/ckeditor.js',
            ],

        ];
    }
}
