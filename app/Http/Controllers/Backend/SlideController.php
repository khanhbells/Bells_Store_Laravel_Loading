<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSlideRequest;
use App\Http\Requests\UpdateSlideRequest;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Models\SlideCatalogue;
// use App\Models\Slide;
use App\Services\Interfaces\SlideServiceInterface as SlideService;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideRepository;

//Neu muon view hieu duoc controller thi phai compact
class SlideController extends Controller
{
    protected $slideService;
    protected $provinceRepository;
    protected $slideRepository;
    protected $language;
    public function __construct(SlideService $slideService, ProvinceRepository $provinceRepository, SlideRepository $slideRepository)
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
        $this->slideService = $slideService;
        $this->provinceRepository = $provinceRepository;
        $this->slideRepository = $slideRepository;
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', 'slide.index');
            // dd($request);
            $slides = $this->slideService->paginate($request);
            $languageId = $this->language;
            // dd($slides);
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js',
                    'backend/library/slide.js',
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'slide'
            ];
            $config['seo'] = __('message.slide');
            // dd($config['seo']);
            $template = 'backend.slide.slide.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'slides', 'languageId'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'slide.create');
            $provinces = $this->provinceRepository->all();
            $config = $this->configData();
            $config['seo'] = __('message.slide');
            $config['method'] = 'create';
            $config['checked'] = true;
            $template = 'backend.slide.slide.store';

            // Truyền slideCatalogues vào view
            return view('backend.dashboard.layout', compact('template', 'config', 'provinces'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreSlideRequest $request)
    {
        if ($this->slideService->create($request, $this->language)) {
            return redirect()->route('slide.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('slide.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'slide.update');
            $slide = $this->slideRepository->findById($id);
            // dd($slide);
            $slideItem = $this->slideService->convertSlideArray($slide->item[$this->language]);
            $config = $this->configData();
            // dd($slide->setting['navigate']);
            $template = 'backend.slide.slide.store';
            $config['seo'] = __('message.slide');
            $config['method'] = 'edit';
            $config['checked'] = ($slide->setting['arrow'] ?? null) === 'accept';
            return view('backend.dashboard.layout', compact('template', 'config', 'slide', 'slideItem'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function update($id, UpdateSlideRequest $request)
    {
        if ($this->slideService->update($id, $request, $this->language)) {
            return redirect()->route('slide.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('slide.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'slide.destroy');
            $config['seo'] = __('message.slide');
            $slide = $this->slideRepository->findById($id);
            $template = 'backend.slide.slide.delete';
            return view('backend.dashboard.layout', compact('template', 'slide', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->slideService->destroy($id)) {
            return redirect()->route('slide.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('slide.index')->with('error', 'Xóa bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'css' => ['backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'],
            'js' => [
                'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'backend/plugin/ckfinder_2/ckfinder.js',
                'backend/library/slide.js',
            ],

        ];
    }
}
