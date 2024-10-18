<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuChildrenRequest;
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
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;

//Neu muon view hieu duoc controller thi phai compact
class MenuController extends Controller
{
    protected $menuService;
    protected $menuRepository;
    protected $menuCatalogueRepository;
    protected $menuCatalogueService;
    protected $language;
    protected $languageRepository;
    public function __construct(
        MenuService $menuService,
        MenuRepository $menuRepository,
        MenuCatalogueRepository $menuCatalogueRepository,
        MenuCatalogueService $menuCatalogueService,
        LanguageRepository $languageRepository,
    ) {
        $this->menuService = $menuService;
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->menuCatalogueService = $menuCatalogueService;
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
            $this->authorize('modules', 'menu.index');
            // dd($request);
            $menuCatalogues = $this->menuCatalogueService->paginate($request, 1);
            // dd($menus); //hien thi thanh vien
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'
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
        if ($this->menuService->save($request, $this->language)) {
            $menuCatalogueId = $request->input('menu_catalogue_id');
            return redirect()->route('menu.edit', ['id' => $menuCatalogueId])->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('menu.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'menu.update');
            $language = $this->language;
            $menus = $this->menuRepository->findByCondition([
                ['menu_catalogue_id', '=', $id]
            ], TRUE, [
                'languages' => function ($query) use ($language) {
                    $query->where('language_id', $language);
                }
            ], ['order', 'DESC']);
            $menuCatalogue = $this->menuCatalogueRepository->findById($id);
            $config = $this->configData();
            $template = 'backend.menu.menu.show';
            $config['seo'] = __('message.menu');
            $config['method'] = 'edit';
            return view('backend.dashboard.layout', compact('template', 'config', 'menus', 'id', 'menuCatalogue'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }

    public function editMenu($id)
    {
        try {
            $this->authorize('modules', 'menu.update');
            $language = $this->language;
            $menuCatalogues = $this->menuCatalogueRepository->all();
            $menuCatalogue = $this->menuCatalogueRepository->findById($id);
            $menus = $this->menuRepository->findByCondition([
                ['menu_catalogue_id', '=', $id],
                ['parent_id', '=', 0]
            ], TRUE, [
                'languages' => function ($query) use ($language) {
                    $query->where('language_id', $language);
                }
            ], ['order', 'DESC']);
            $menuList = $this->menuService->convertMenu($menus);
            $config = $this->configData();
            $config['seo'] = __('message.menu');
            $config['method'] = 'update';
            $template = 'backend.menu.menu.store';
            return view('backend.dashboard.layout', compact('template', 'config', 'menuList', 'menuCatalogues', 'menuCatalogue', 'id'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'menu.destroy');
            $config['seo'] = __('message.menu');
            $menuCatalogue = $this->menuCatalogueRepository->findById($id);
            $template = 'backend.menu.menu.delete';
            return view('backend.dashboard.layout', compact('template', 'menuCatalogue', 'config'));
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

    public function children($id)
    {
        try {
            $this->authorize('modules', 'menu.create');
            $language = $this->language;
            $menu = $this->menuRepository->findById($id, ['*'], ['languages' => function ($query) use ($language) {
                $query->where('language_id', $language);
            }]);
            $menuList = $this->menuService->getAndconvertMenu($menu, $this->language);
            $config = $this->configData();
            $config['seo'] = __('message.menu');
            $config['method'] = 'children';
            $template = 'backend.menu.menu.children';
            return view('backend.dashboard.layout', compact('template', 'config', 'menu', 'menuList'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function saveChildren($id, StoreMenuChildrenRequest $request)
    {
        $menu = $this->menuRepository->findById($id);
        if ($this->menuService->saveChildren($request, $this->language, $menu)) {
            return redirect()->route('menu.edit', ['id' => $menu->menu_catalogue_id])->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('menu.edit', ['id' => $menu->menu_catalogue_id])->with('error', 'Thêm mới bản ghi không thành công');
    }

    public function translate(int $languageId = 1, int $id = 0)
    {
        $language = $this->languageRepository->findById($languageId);
        $menuCatalogue = $this->menuCatalogueRepository->findById($id);
        $currentLanguage = $this->language;
        $menus = $this->menuRepository->findByCondition([
            ['menu_catalogue_id', '=', $id],
        ], TRUE, [
            'languages' => function ($query) use ($currentLanguage) {
                $query->where('language_id', $currentLanguage);
            }
        ], ['lft', 'asc']);
        $menus = buildMenu($this->menuService->findMenuItemTranslate($menus, $currentLanguage, $languageId));


        $config['seo'] = __('message.menu');
        $config['method'] = 'translate';
        $template = 'backend.menu.menu.translate';
        return view('backend.dashboard.layout', compact('template', 'config', 'language', 'menuCatalogue', 'menus', 'languageId'));
    }

    public function saveTranslate(Request $request, $languageId = 1)
    {
        if ($this->menuService->saveTranslateMenu($request, $languageId)) {
            return redirect()->route('menu.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('menu.index')->with('error', 'Cập nhật bản ghi không thành công');
    }

    private function configData()
    {
        return [
            'css' => ['backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'],
            'js' => [
                'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/menu.js',
                'backend/js/plugins/nestable/jquery.nestable.js'
            ],

        ];
    }
}
