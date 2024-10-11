<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Menu;
use App\Models\Language;
use Illuminate\Http\Request;
// use App\Models\Menu;
use App\Services\Interfaces\MenuServiceInterface as MenuService;
use App\Repositories\Interfaces\MenuRepositoryInterface as MenuRepository;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface as MenuCatalogueRepository;
use App\Services\Interfaces\MenuCatalogueServiceInterface as MenuCatalogueService;

//Neu muon view hieu duoc controller thi phai compact
class MenuController extends Controller
{
    protected $menuService;
    protected $menuRepository;
    protected $menuCatalogueRepository;
    protected $menuCatalogueService;
    protected $language;
    public function __construct(
        MenuService $menuService,
        MenuRepository $menuRepository,
        MenuCatalogueRepository $menuCatalogueRepository,
        MenuCatalogueService $menuCatalogueService
    ) {
        $this->menuService = $menuService;
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->menuCatalogueService = $menuCatalogueService;
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
            $this->authorize('modules', 'menu.index');
            // dd($request);
            $menuCatalogues = $this->menuCatalogueService->paginate($request, 1);
            // dd($menus); //hien thi thanh vien
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'MenuCatalogue'
            ];
            $config['seo'] = __('message.menu');
            // dd($config['seo']);
            $template = 'backend.menu.menu.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'menuCatalogues'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'menu.create');
            $menuCatalogues = $this->menuCatalogueRepository->all();
            $config = $this->configData();
            $config['seo'] = __('message.menu');
            $config['method'] = 'create';
            $template = 'backend.menu.menu.store';

            // Truyền menus vào view
            return view('backend.dashboard.layout', compact('template', 'config', 'menuCatalogues'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreMenuRequest $request)
    {
        if ($this->menuService->create($request, $this->language)) {
            return redirect()->route('menu.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('menu.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'menu.update');
            $menu = $this->menuRepository->findById($id);
            // dd($menu);
            $menus = Menu::where('publish', 2)->get();
            // dd($provinces);
            // dd($province);
            $config = $this->configData();
            $template = 'backend.menu.menu.store';

            $config['seo'] = __('message.menu');
            $config['method'] = 'edit';
            return view('backend.dashboard.layout', compact('template', 'config', 'provinces', 'menu', 'menus'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function update($id, UpdateMenuRequest $request)
    {
        if ($this->menuService->update($id, $request)) {
            return redirect()->route('menu.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('menu.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'menu.destroy');
            $config['seo'] = __('message.menu');
            $menu = $this->menuRepository->findById($id);
            $template = 'backend.menu.menu.delete';
            return view('backend.dashboard.layout', compact('template', 'menu', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->menuService->destroy($id)) {
            return redirect()->route('menu.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('menu.index')->with('error', 'Xóa bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'css' => ['https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/menu.js',
            ],

        ];
    }
}
