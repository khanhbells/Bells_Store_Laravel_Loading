<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserCatalogueRequest;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Services\Interfaces\UserCatalogueServiceInterface as UserCatalogueService;
use App\Repositories\Interfaces\UserCatalogueRepositoryInterface as UserCatalogueRepository;
// use App\Repositories\Interfaces\UserCatalogueRepositoryInterface as UserCatalogueRepository;
//Neu muon view hieu duoc controller thi phai compact
class UserCatalogueController extends Controller
{
    protected $UserCatalogueService;
    protected $UserCatalogueRepository;
    public function __construct(UserCatalogueService $UserCatalogueService, UserCatalogueRepository $UserCatalogueRepository)
    {
        $this->UserCatalogueService = $UserCatalogueService;
        $this->UserCatalogueRepository = $UserCatalogueRepository;
    }
    public function index(Request $request)
    {
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
    }
    public function create()
    {
        // dd($provinces);
        // dd($province);
        $config['seo'] = config('app.usercatalogue');
        $config['method'] = 'create';
        $template = 'backend.user.catalogue.store';
        return view('backend.dashboard.layout', compact('template', 'config'));
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
        $userCatalogue = $this->UserCatalogueRepository->findById($id);
        // dd($user);
        // dd($provinces);
        // dd($province);
        $template = 'backend.user.catalogue.store';

        $config['seo'] = config('app.usercatalogue');
        $config['method'] = 'edit';
        return view('backend.dashboard.layout', compact('template', 'config', 'userCatalogue'));
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
        $config['seo'] = config('app.usercatalogue');
        $userCatalogue = $this->UserCatalogueRepository->findById($id);
        $template = 'backend.user.catalogue.delete';
        return view('backend.dashboard.layout', compact('template', 'userCatalogue', 'config'));
    }
    public function destroy($id)
    {
        if ($this->UserCatalogueService->destroy($id)) {
            return redirect()->route('user.catalogue.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('user.catalogue.index')->with('error', 'Xóa bản ghi không thành công');
    }
}
