<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Models\Language;
use App\Models\Promotion;
use Illuminate\Http\Request;
use App\Models\PromotionCatalogue;
// use App\Models\Promotion;
use App\Services\Interfaces\PromotionServiceInterface as PromotionService;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
use Illuminate\Support\Collection;
//Neu muon view hieu duoc controller thi phai compact
class PromotionController extends Controller
{
    protected $promotionService;
    protected $promotionRepository;
    protected $language;
    protected $languageRepository;
    public function __construct(
        PromotionService $promotionService,
        PromotionRepository $promotionRepository,
        LanguageRepository $languageRepository,
    ) {
        $this->promotionService = $promotionService;
        $this->promotionRepository = $promotionRepository;
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
            $this->authorize('modules', 'promotion.index');
            // dd($request);
            $promotions = $this->promotionService->paginate($request);
            // dd($promotions); //hien thi thanh vien
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'Promotion'
            ];
            $config['seo'] = __('message.promotion');
            // dd($config['seo']);
            $template = 'backend.promotion.promotion.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'promotions'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'promotion.create');
            $config = $this->configData();
            $config['seo'] = __('message.promotion');
            $config['method'] = 'create';
            $template = 'backend.promotion.promotion.store';

            // Truyền promotionCatalogues vào view
            return view('backend.dashboard.layout', compact('template', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StorePromotionRequest $request)
    {
        if ($this->promotionService->create($request, $this->language)) {
            return redirect()->route('promotion.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('promotion.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'promotion.update');
            $promotion = $this->promotionRepository->findById($id);
            $promotion->description = $promotion->description[$this->language];
            $modelClass = loadClass($promotion->model);
            $promotionItem = convertArrayByKey($modelClass->findByCondition(
                ...array_values($this->menuItemAgrument($promotion->model_id))
            ), ['id', 'name.languages', 'image']);

            $config = $this->configData();
            $template = 'backend.promotion.promotion.store';
            $album = json_decode($promotion->album);
            $config['seo'] = __('message.promotion');
            $config['method'] = 'edit';
            return view('backend.dashboard.layout', compact('template', 'config', 'promotion', 'album', 'promotionItem'));
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
    public function update($id, UpdatePromotionRequest $request)
    {
        if ($this->promotionService->update($id, $request, $this->language)) {
            return redirect()->route('promotion.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('promotion.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'promotion.destroy');
            $config['seo'] = __('message.promotion');
            $promotion = $this->promotionRepository->findById($id);
            $template = 'backend.promotion.promotion.delete';
            return view('backend.dashboard.layout', compact('template', 'promotion', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->promotionService->destroy($id)) {
            return redirect()->route('promotion.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('promotion.index')->with('error', 'Xóa bản ghi không thành công');
    }
    public function translate($languageId, $promotionId)
    {
        try {
            $this->authorize('modules', 'promotion.translate');

            $promotion = $this->promotionRepository->findById($promotionId);
            $promotion->jsonDescription = $promotion->description;
            $promotion->description = $promotion->description[$this->language];

            $promotionTranslate = new \stdClass;
            $promotionTranslate->description = ($promotion->jsonDescription[$languageId]) ?? '';


            $translate = $this->languageRepository->findById($languageId);
            $config = $this->configData();
            $config['seo'] = __('message.promotion');
            $config['method'] = 'create';
            $template = 'backend.promotion.promotion.translate';

            // Truyền promotionCatalogues vào view
            return view('backend.dashboard.layout', compact(
                'template',
                'config',
                'promotion',
                'translate',
                'promotionTranslate'
            ));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function saveTranslate(Request $request)
    {
        if ($this->promotionService->saveTranslate($request, $this->language)) {
            return redirect()->route('promotion.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('promotion.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'backend/plugin/datetimepicker-master/build/jquery.datetimepicker.min.css',
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugin/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/promotion.js',
                'backend/plugin/ckeditor/ckeditor.js',
                'backend/plugin/datetimepicker-master/build/jquery.datetimepicker.full.js',
            ],

        ];
    }
}