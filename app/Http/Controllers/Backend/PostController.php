<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\DeletePostRequest;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Services\Interfaces\PostServiceInterface as PostService;
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
use App\Classes\Nestedsetbie;
use App\Models\Language;
use App\Models\Post;

// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class PostController extends Controller
{
    protected $postService;
    protected $postRepository;
    protected $nestedset;
    protected $language;
    public function __construct(PostService $postService, PostRepository $postRepository)
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->postService = $postService;
        $this->postRepository = $postRepository;
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
            $this->authorize('modules', 'post.index');
            $languageId = $this->language;
            $posts = $this->postService->paginate($request, $this->language);
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'Post'
            ];
            $config['seo'] = config('app.post');
            $template = 'backend.post.post.index';
            $dropdown = $this->nestedset->Dropdown();
            return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', 'posts', 'languageId'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'post.create');
            $config = $this->configData();
            $config['seo'] = config('app.post');
            $config['method'] = 'create';
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.post.post.store';
            return view('backend.dashboard.layout', compact('template', 'config', 'dropdown'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StorePostRequest $request)
    {
        // dd($request);
        if ($this->postService->create($request, $this->language)) {
            return redirect()->route('post.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('post.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'post.update');
            $post = $this->postRepository->getPostById($id, $this->language);
            $config = $this->configData();
            $dropdown = $this->nestedset->Dropdown();
            $template = 'backend.post.post.store';
            $config['seo'] = config('app.post');
            $config['method'] = 'edit';
            $album = json_decode($post->album);
            return view('backend.dashboard.layout', compact('template', 'config', 'post', 'dropdown', 'album'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    //--------------------------------------------------------------
    public function update($id, UpdatePostRequest $request)
    {
        if ($this->postService->update($id, $request, $this->language)) {
            return redirect()->route('post.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('post.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'post.destroy');
            $config['seo'] = config('app.post');
            $post = $this->postRepository->getPostById($id, $this->language);
            $template = 'backend.post.post.delete';
            return view('backend.dashboard.layout', compact('template', 'post', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->postService->destroy($id)) {
            return redirect()->route('post.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('post.index')->with('error', 'Xóa bản ghi không thành công');
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
    public function catalogue($post)
    {
        dd($post->post_catalogues);
        // foreach($post as $key->$val)
    }
}
