<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use App\Models\UserCatalogue;
// use App\Models\User;
use App\Services\Interfaces\UserServiceInterface as UserService;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;

//Neu muon view hieu duoc controller thi phai compact
class UserController extends Controller
{
    protected $userService;
    protected $provinceRepository;
    protected $userRepository;
    public function __construct(UserService $userService, ProvinceRepository $provinceRepository, UserRepository $userRepository)
    {
        $this->userService = $userService;
        $this->provinceRepository = $provinceRepository;
        $this->userRepository = $userRepository;
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', 'user.index');
            // dd($request);
            $users = $this->userService->paginate($request);
            // dd($users); //hien thi thanh vien
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'User'
            ];
            $config['seo'] = config('app.user');
            // dd($config['seo']);
            $template = 'backend.user.user.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'users'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'user.create');
            $provinces = $this->provinceRepository->all();
            $userCatalogues = UserCatalogue::where('publish', 2)->get(); // Lấy danh sách nhóm thành viên đã được publish
            $config = $this->configData();
            $config['seo'] = config('app.user');
            $config['method'] = 'create';
            $template = 'backend.user.user.store';

            // Truyền userCatalogues vào view
            return view('backend.dashboard.layout', compact('template', 'config', 'provinces', 'userCatalogues'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreUserRequest $request)
    {
        if ($this->userService->create($request)) {
            return redirect()->route('user.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('user.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'user.update');
            $user = $this->userRepository->findById($id);
            // dd($user);
            $provinces = $this->provinceRepository->all();
            $userCatalogues = UserCatalogue::where('publish', 2)->get();
            // dd($provinces);
            // dd($province);
            $config = $this->configData();
            $template = 'backend.user.user.store';

            $config['seo'] = config('app.user');
            $config['method'] = 'edit';
            return view('backend.dashboard.layout', compact('template', 'config', 'provinces', 'user', 'userCatalogues'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function update($id, UpdateUserRequest $request)
    {
        if ($this->userService->update($id, $request)) {
            return redirect()->route('user.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('user.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'user.destroy');
            $config['seo'] = config('app.user');
            $user = $this->userRepository->findById($id);
            $template = 'backend.user.user.delete';
            return view('backend.dashboard.layout', compact('template', 'user', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->userService->destroy($id)) {
            return redirect()->route('user.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('user.index')->with('error', 'Xóa bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'css' => ['backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'],
            'js' => [
                'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'backend/plugin/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
            ],

        ];
    }
}
