<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserCatalogueRequest;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Services\Interfaces\UserCatalogueServiceInterface as UserCatalogueService;
use App\Repositories\Interfaces\UserCatalogueRepositoryInterface as UserCatalogueRepository;
use App\Repositories\Interfaces\PermissionRepositoryInterface as PermissionRepository;
// use App\Repositories\Interfaces\UserCatalogueRepositoryInterface as UserCatalogueRepository;
//Neu muon view hieu duoc controller thi phai compact
class UserCatalogueController extends Controller
{
    protected $UserCatalogueService;
    protected $UserCatalogueRepository;
    protected $permissionRepository;
    public function __construct(UserCatalogueService $UserCatalogueService, UserCatalogueRepository $UserCatalogueRepository, PermissionRepository $permissionRepository)
    {
        $this->UserCatalogueService = $UserCatalogueService;
        $this->UserCatalogueRepository = $UserCatalogueRepository;
        $this->permissionRepository = $permissionRepository;
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', 'user.catalogue.index');
            $userCatalogues = $this->UserCatalogueService->paginate($request);
            // dd($users); //hien thi thanh vien


            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'UserCatalogue'
            ];
            $config['seo'] = config('app.usercatalogue');
            // dd($config['seo']);
            $template = 'backend.user.catalogue.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'userCatalogues'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'user.catalogue.create');
            // dd($provinces);
            // dd($province);
            $config['seo'] = config('app.usercatalogue');
            $config['method'] = 'create';
            $template = 'backend.user.catalogue.store';
            return view('backend.dashboard.layout', compact('template', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreUserCatalogueRequest $request)
    {
        if ($this->UserCatalogueService->create($request)) {
            return redirect()->route('user.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('user.catalogue.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'user.catalogue.update');
            $userCatalogue = $this->UserCatalogueRepository->findById($id);
            // dd($user);
            // dd($provinces);
            // dd($province);
            $template = 'backend.user.catalogue.store';

            $config['seo'] = config('app.usercatalogue');
            $config['method'] = 'edit';
            return view('backend.dashboard.layout', compact('template', 'config', 'userCatalogue'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function update($id, StoreUserCatalogueRequest $request)
    {
        if ($this->UserCatalogueService->update($id, $request)) {
            return redirect()->route('user.catalogue.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('user.catalogue.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'user.catalogue.destroy');
            $config['seo'] = config('app.usercatalogue');
            $userCatalogue = $this->UserCatalogueRepository->findById($id);
            $template = 'backend.user.catalogue.delete';
            return view('backend.dashboard.layout', compact('template', 'userCatalogue', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->UserCatalogueService->destroy($id)) {
            return redirect()->route('user.catalogue.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('user.catalogue.index')->with('error', 'Xóa bản ghi không thành công');
    }

    public function permission()
    {
        try {
            $this->authorize('modules', 'user.catalogue.permission');
            $userCatalogues = $this->UserCatalogueRepository->all(['permissions']);
            $permissions = $this->permissionRepository->all();
            $config['seo'] = __('message.userCatalogue');
            $template = 'backend.user.catalogue.permission';
            return view('backend.dashboard.layout', compact('template', 'userCatalogues', 'permissions', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function updatePermission(Request $request)
    {
        if ($this->UserCatalogueService->setPermission($request)) {
            return redirect()->route('user.catalogue.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('user.catalogue.index')->with('error', 'Cập nhật bản ghi không thành công');
        // $permission = $request->input('permission');
    }
}
