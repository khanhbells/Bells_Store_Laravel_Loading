<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostCatalogueRequest;
use App\Http\Requests\UpdatePostCatalogueRequest;
use App\Http\Requests\DeletePostCatalogueRequest;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Services\Interfaces\PostCatalogueServiceInterface as PostCatalogueService;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface as PostCatalogueRepository;
use App\Classes\Nestedsetbie;
use App\Models\Language;
// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class PostCatalogueController extends Controller
{
    protected $postCatalogueService;
    protected $postCatalogueRepository;
    protected $nestedset;
    protected $language;
    public function __construct(PostCatalogueService $postCatalogueService, PostCatalogueRepository $postCatalogueRepository, Nestedsetbie $nestedset)
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->postCatalogueService = $postCatalogueService;
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->initialize();
    }
    public function initialize()
    {
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' => $this->language,
        ]);
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', 'post.catalogue.index');

            $postCatalogues = $this->postCatalogueService->paginate($request, $this->language);
            // dd($postCatalogues); //hien thi thanh vien


            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'PostCatalogue'
            ];
            $config['seo'] = __('message.postCatalogue');
            // dd($config['seo']);
            $template = 'backend.post.catalogue.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'postCatalogues'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'post.catalogue.create');
            $config = $this->configData();
            $config['seo'] = __('message.postCatalogue');
            $config['method'] = 'create';
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.post.catalogue.store';
            return view('backend.dashboard.layout', compact('template', 'config', 'dropdown'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StorePostCatalogueRequest $request)
    {
        // dd($request);
        if ($this->postCatalogueService->create($request, $this->language)) {
            return redirect()->route('post.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('post.catalogue.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'post.catalogue.update');
            $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById($id, $this->language);
            $config = $this->configData();
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.post.catalogue.store';
            $config['seo'] = __('message.postCatalogue');
            $config['method'] = 'edit';
            $album = json_decode($postCatalogue->album);
            return view('backend.dashboard.layout', compact('template', 'config', 'postCatalogue', 'dropdown', 'album'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    //--------------------------------------------------------------
    public function update($id, UpdatePostCatalogueRequest $request)
    {
        // dd($request);
        if ($this->postCatalogueService->update($id, $request, $this->language)) {
            return redirect()->route('post.catalogue.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('post.catalogue.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'post.catalogue.destroy');
            $config['seo'] = __('message.postCatalogue');
            $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById($id, $this->language);
            $template = 'backend.post.catalogue.delete';
            return view('backend.dashboard.layout', compact('template', 'postCatalogue', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id, DeletePostCatalogueRequest $request)
    {
        if ($this->postCatalogueService->destroy($id, $this->language)) {
            return redirect()->route('post.catalogue.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('post.catalogue.index')->with('error', 'Xóa bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'js' => [
                'backend/plugin/ckeditor/ckeditor.js',
                'backend/plugin/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];
    }
}
